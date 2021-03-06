<rss version="2.0"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:content="http://purl.org/rss/1.0/modules/content/">
	<channel>
		<title><?php echo $feed_name; ?></title>
		<link><?php echo $feed_url; ?></link>
		<description><?php echo $page_description; ?></description>
		<dc:creator><?php echo $creator_email; ?></dc:creator>
		<dc:rights>Copyright <?php echo gmdate("Y", time()); ?></dc:rights>
		<admin:generatorAgent rdf:resource="http://www.cafevariome.org/" />
		<?php foreach($posts->result() as $entry): ?>
		<item>
			<title><?php echo xml_convert($entry->post_title); ?></title>
			<link><?php echo site_url('feed/entry/' . url_title($entry->id)) ?></link>
			<guid><?php echo site_url('feed/entry/' . url_title($entry->post_title)) ?></guid>
			<description><?php echo $entry->post_body; ?></description>
			<pubDate><?php echo $entry->post_date;?></pubDate>
		</item>
		<?php endforeach; ?>
	</channel>
</rss> 
