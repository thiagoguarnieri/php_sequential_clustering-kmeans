<?php
//number of clusters
$k_number = $argv[1];

//centroid data structure
$centroids = array();

//number of elements in each cluster
$cluster_sizes = array();

//dataset
$dataset = $argv[2];

$output = $argv[3];

//sum of errors
$sae = array();
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//READING THE FILE
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
echo("Reading file\n");

//file handler
$handle = fopen($dataset, "r");

//jump header
$header = trim(fgets($handle),"\n");

//get the centroids 
echo("\nMaking the centroids\n");

//the centroids will be the k first ones
$idxControl = 0;
for($i = 0; !feof($handle) && $idxControl < $k_number; $i++)
{
	$tmp = fgetcsv($handle);
	//centroid instance
	$centroids[] = $tmp;
	//size of the cluster
	$cluster_sizes[] = 0;
	//wcss of the cluster
	$sae[] = 0;
	//current cluster number
	$idxControl++;
}

//returning to the beginning
rewind($handle);

//jump header
$header = trim(fgets($handle),"\n");

//put header
file_put_contents($output,"cluster_list\n");

//reading the rest of data
$linebuffer = array();
$buffercount = 0;

while (!feof($handle))
{
	if($element = fgetcsv($handle))
	{ 
		$linebuffer[] = $element;
		$buffercount++;
		
		if($buffercount == 100000)
		{
			$text = "";
			foreach($linebuffer as $lbfr)
			{
				//updating centroids
				$centroids = update_centroid($lbfr, $centroids, $cluster_sizes, $sae);
				//saving cluster list
				$text .= $lbfr[count($lbfr) - 1]."\n";
			}
			
			//reseting and saving
			$buffercount = 0;
			$linebuffer = array();
			file_put_contents($output,$text,FILE_APPEND);
			echo("-");
		}
	}
}

//the rest of the array (elements after last buffering)
if(count($linebuffer) > 0)
{
	$text = "";
	foreach($linebuffer as $lbfr)
	{
		//updating centroids
		update_centroid($lbfr, $centroids, $cluster_sizes, $sae);
		
		//saving the associated cluster of the current instance
		$text .= $lbfr[count($lbfr) - 1]."\n";
	}
	
	//reseting and saving
	$buffercount = 0;
	$linebuffer = array();
	file_put_contents($output,$text,FILE_APPEND);
	echo("-");
}

fclose($handle);

//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//OUTPUTTNG RESULTS
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
echo("\nStatistics\n");
$wcss = 0;
for($i = 0; $i < $k_number; $i++){
	$wcss = $sae[$i] / $cluster_sizes[$i];
}

echo("\nWSS for $k_number clusters:\n"  . ($wcss/$k_number) . "\n");
echo("\nCentroids\n");
print_centroids($centroids);
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//UPDATING CENTROIDS
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//find the nearest centroid and return the new averaged one
function update_centroid(&$elem, &$centrds, &$sizes, &$errors)
{
	$distances = array();
	$smalest = euclidean($elem, $centrds[0]);
	$smalest_idx = 0;
	
	//find the nearest centroid
	for($i = 0; $i < count($centrds); $i++)
	{
		$distances[$i] = euclidean($elem, $centrds[$i]);
		
		//find the smallest distance
		if($distances[$i] < $smalest)
		{
			$smalest = $distances[$i];
			$smalest_idx = $i;
		}
	}
	
	//sum of errors
	$errors[$smalest_idx] = $errors[$smalest_idx] + $smalest;
	
	//number of elements of cluster
	$sizes[$smalest_idx] += 1;
	
	//cluster columns
	$elem[count($elem)] = $smalest_idx;
	
	//update centroids
	for($i = 0; $i < count($centrds[$smalest_idx]); $i++)
	{
		$centrds[$smalest_idx][$i] = $centrds[$smalest_idx][$i] + (1/$sizes[$smalest_idx])*($elem[$i] - $centrds[$smalest_idx][$i]);
	}
	
}
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//SUPPORT FUNCTIONS
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//--------------------------------------------------------------------
//calculate euclidean distance
function euclidean($p1, $p2)
{
	$distance = 0;
	
	//for each dimension
	for($i = 0; $i < count($p1); $i++)
	{
		$distance += (($p2[$i] - $p1[$i])**2);
	}
	
	//returns the euclidean distance
	return sqrt($distance);
}
//--------------------------------------------------------------------
function print_centroids($centrds)
{
	foreach($centrds as $cent)
	{
			foreach($cent as $dim)
			{
				echo("$dim, ");
			}
			echo("\n\n");
	}
}








