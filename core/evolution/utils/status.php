<?php

/**
 * Installation status - success!
 * 
 * @author Nate Ferrero
 */
function raw($in) {
	return preg_replace('/[^a-zA-Z\-]/', '\\\\$0', $in);
}
$open = raw('<!--evolution-status-[-->');
$close = raw('<!--evolution-status-]-->');
$r = '/' . $open . '.*?' . $close . '/s';
$html = file_get_contents(__DIR__ . '/../../../status');
$html = preg_replace('/background: .+?\;/', 'background: #060;', $html);
$html = preg_replace('/not installed/', 'installed!', $html);
echo preg_replace($r, '
	<p>Good news! It looks like you are ready to use EvolutionSDK. If you have any issues whatsoever, you can help us resolve them by reporting them to the EvolutionSDK repository on GitHub.</p>
	<p>Next steps:</p>
	<ul><li>Create or import a site from the control panel.</li></ul>
	<p><br /><a target="_top" href="http://evolution.dev">Click to open the EvolutionSDK Control Panel &raquo;</a>
	</p>', $html);