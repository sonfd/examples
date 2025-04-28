<?php

/**
 * Batch update entities example.
 *
 * Goes in MY_MODULE.deploy.php.
 */
function MY_MODULE_deploy_NAME(array &$sandbox) {
  $batch_size = 25;
  $entity_type = 'ENTITY_TYPE';
  $bundle = 'ENTITY_BUNDLE';
  $bundle_identifier = 'type'; // May also be: bundle, vid.

  if (!isset($sandbox['num_processed'])) {
    $sandbox['num_processed'] = 0;
  }

  if (empty($sandbox['entity_ids'])) {
    $sandbox['entity_ids'] = \Drupal::entityQuery($entity_type)
	    ->accessCheck(FALSE)
      ->condition($bundle_identifier, $bundle)
      ->execute();

    if (is_array($sandbox['entity_ids'])) {
      $sandbox['total'] = count($sandbox['entity_ids']);
    }
  }

  if (!empty($sandbox['entity_ids'])) {
    $current_batch_ids = array_slice($sandbox['entity_ids'], $sandbox['num_processed'], $batch_size);

    $current_batch_entities = \Drupal::entityTypeManager()
      ->getStorage($entity_type)
      ->loadMultiple($current_batch_ids);

    foreach ($current_batch_entities as $entity) {
      // Do stuff to entity
      // ...
      $entity->save();
      $sandbox['num_processed']++;
    }
  }

  if (!empty($sandbox['total'])) {
    $sandbox['#finished'] = $sandbox['num_processed'] / $sandbox['total'];
  }
  else {
    $sandbox['#finished'] = 1;
  }
}
