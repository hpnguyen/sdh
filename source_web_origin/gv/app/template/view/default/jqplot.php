<?php 
$help = Helper::getHelper('functions/util');
$gvRootUrl = $help->getGvRootURL();
?>
<script type="text/javascript" src="<?php echo $gvRootUrl ?>/js/jqplot_dist/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $gvRootUrl ?>/js/jqplot_dist/jquery.jqplot.min.js"></script>
<?php
$listScript = array(
'jqplot.barRenderer.min.js','jqplot.BezierCurveRenderer.min.js',
'jqplot.blockRenderer.min.js','jqplot.bubbleRenderer.min.js',
'jqplot.canvasAxisLabelRenderer.min.js','jqplot.canvasAxisTickRenderer.min.js',
'jqplot.canvasOverlay.min.js','jqplot.canvasTextRenderer.min.js',
'jqplot.categoryAxisRenderer.min.js','jqplot.ciParser.min.js',
'jqplot.cursor.min.js','jqplot.dateAxisRenderer.min.js',
'jqplot.donutRenderer.min.js','jqplot.dragable.min.js',
'jqplot.enhancedLegendRenderer.min.js','jqplot.funnelRenderer.min.js',
'jqplot.highlighter.min.js','jqplot.json2.min.js',
'jqplot.logAxisRenderer.min.js','jqplot.mekkoAxisRenderer.min.js',
'jqplot.mekkoRenderer.min.js','jqplot.meterGaugeRenderer.min.js',
'jqplot.mobile.min.js','jqplot.ohlcRenderer.min.js',
'jqplot.pieRenderer.min.js','jqplot.pointLabels.min.js',
'jqplot.pyramidAxisRenderer.min.js','jqplot.pyramidGridRenderer.min.js',
'jqplot.pyramidRenderer.min.js','jqplot.trendline.min.js'
);
foreach ($listScript as $key => $value) {
?>
<script type="text/javascript" src="<?php echo $gvRootUrl ?>/js/jqplot_dist/plugins/<?php echo $value ?>"></script>
<?php
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo $help->getGvRootURL() ?>/js/jqplot_dist/jquery.jqplot.min.css" />
<div id="<?php echo $canvasId ?>" style="<?php echo $canvasStyle ?>"></div>
<script type="text/javascript">
	$(document).ready(function(){
		var s1 = [<?php echo $jqplotData ?>];
	         
	    var plot8 = $.jqplot('<?php echo $canvasId ?>', [s1], {
	    	<?php echo $jqplotOptionsString ?> 
	    });
	});
</script>