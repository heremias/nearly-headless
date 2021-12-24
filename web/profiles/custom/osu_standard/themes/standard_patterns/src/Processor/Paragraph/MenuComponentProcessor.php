<?php

namespace Drupal\standard_patterns\Processor\Paragraph;

class MenuComponentProcessor extends ParagraphProcessor {

  public static function process(&$variables) {
  	// Fetch the paragraph.
  	$p = $variables['paragraph'];

  	// Configure the right menu.
  	$menuName = 'main';

    // Get menu tree parameters.
    $params = new \Drupal\Core\Menu\MenuTreeParameters();
    $params->setMaxDepth(1);
    $params->setRoot($p->get('root')->value);
    $params->excludeRoot();
    $trail = \Drupal::service('menu.active_trail');
    $params->setActiveTrail($trail->getActiveTrailIds($menuName));

    // Load and transform the tree.
    $service = \Drupal::service('menu.link_tree');
    $tree = $service->load($menuName, $params);
    $tree = $service->transform($tree, [
      ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort']
    ]);

    // Iterate over our tree.
	$links = [];
	$linkManager = \Drupal::entityTypeManager()->getStorage('menu_link_content');
	foreach ($tree as $key => $entry) {
	  // Load content for this link.
  	  $parts = explode(':', $key);
  	  $uuid = $parts[1];
  	  $link = current($linkManager->loadByProperties(['uuid' => $uuid]));
      $url = $link->getUrlObject();
      $links[] = [
      	'title' => $link->get('title')->value,
      	'href' => $url->toString() == '' ? '#' : $url->toString(),
      	'weight' => $link->get('weight')->value,
      	'active' => $entry->inActiveTrail
      ];
    }
    usort($links, function ($a, $b) { return ($a['weight'] <=> $b['weight']); });
    $variables['links'] = $links;
  }
}
