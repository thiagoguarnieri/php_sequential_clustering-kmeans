# Incremental (Sequential) k-means

<h1>Description</h1>
Incremental (sequential) k-means implementation in php.<br/>
Based on https://www.cs.princeton.edu/courses/archive/fall08/cos436/Duda/C/sk_means.htm

<p>Useful to cluster large datasets which do not fit in main memory</p>

<h1>How to use</h1>
This script is designed to use with PHP command line interface. Use the following command<br/>
<i>php -f incremental_clustering.php [number_of_clusters] [original_csv] [cluster_output_csv_file]</i>

<h1>Recommendations</h1>
It is important normalize the dataset and, if possible, randomize it.
<p>
The script outputs average Within Cluster Sum of Squares that can be used to decide the optimal number of clusters via elbow method<br/>
https://en.wikipedia.org/wiki/Determining_the_number_of_clusters_in_a_data_set
</p>
