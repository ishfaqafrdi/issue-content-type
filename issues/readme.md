<?php

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Implements hook_install().
 */
function custom_content_type_install() {
  // Create a new content type.
  $content_type = NodeType::create([
    'type' => 'custom_content_type',
    'name' => 'Custom Content Type',
  ]);
  $content_type->save();

  // Define the field storage for the custom taxonomy reference field.
  $taxonomy_field_storage = FieldStorageConfig::create([
    'field_name' => 'custom_taxonomy_reference',
    'entity_type' => 'node',
    'type' => 'entity_reference',
    'settings' => [
      'target_type' => 'taxonomy_term',
      'handler' => 'default',
      'handler_settings' => [
        'target_bundles' => ['your_vocabulary_machine_name'], // Replace with your vocabulary's machine name
      ],
    ],
  ]);
  $taxonomy_field_storage->save();

  // Attach the field to the content type.
  $taxonomy_field = FieldConfig::create([
    'field_name' => 'custom_taxonomy_reference',
    'entity_type' => 'node',
    'bundle' => 'custom_content_type',
    'label' => 'Custom Taxonomy Reference',
    'field_storage' => $taxonomy_field_storage,
  ]);
  $taxonomy_field->save();

  // Define the field storage for the custom user reference field.
  $user_field_storage = FieldStorageConfig::create([
    'field_name' => 'custom_user_reference',
    'entity_type' => 'node',
    'type' => 'entity_reference',
    'settings' => [
      'target_type' => 'user',
      'handler' => 'default',
    ],
  ]);
  $user_field_storage->save();

  // Attach the user reference field to the content type.
  $user_field = FieldConfig::create([
    'field_name' => 'custom_user_reference',
    'entity_type' => 'node',
    'bundle' => 'custom_content_type',
    'label' => 'Custom User Reference',
    'field_storage' => $user_field_storage,
  ]);
  $user_field->save();

  // Load the display settings for the custom content type.
  $entity_type_manager = \Drupal::service('entity_type.manager');
  $entity_display_repository = \Drupal::service('entity_display.repository');

  /** @var \Drupal\node\Entity\NodeType $content_type */
  $content_type = $entity_type_manager->getStorage('node_type')->load('custom_content_type');

  $view_display = $entity_display_repository->getViewDisplay('node', $content_type->id(), 'default');
  $form_display = $entity_display_repository->getFormDisplay('node', $content_type->id(), 'default');

  // Enable the display of your fields on the "default" view mode.
  $view_display->setComponent('custom_taxonomy_reference', [
    'type' => 'entity_reference_label',
    'weight' => 0,
  ]);
  // Enable the display of your fields on the "default" form mode.
  $form_display->setComponent('custom_taxonomy_reference', [
    'type' => 'entity_reference_autocomplete',
    'weight' => 0,
  ]);
  $view_display->setComponent('custom_user_reference', [
    'type' => 'entity_reference_label',
    'weight' => 1,
  ]);
  $view_display->save();


  $form_display->setComponent('custom_user_reference', [
    'type' => 'entity_reference_autocomplete',
    'weight' => 1,
  ]);
  $form_display->save();

  // Flush the cache to recognize the new content type and fields.
  $entity_type_manager->getStorage('node_type')->resetCache();
}
