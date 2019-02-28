<?php

namespace Drupal\paragraphs_entity_embed\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs_entity_embed\EmbeddedParagraphsForm;

/**
 * Provides a form to embed paragraphs.
 * @todo move this to Controller and call it NodeController
 */
class Node extends EmbeddedParagraphsForm {

  const INSERT_METHOD_PROMPTED = 1;

  const INSERT_METHOD_SELECTED = 2;

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {

    $button_value = $this->t('xxxCreate and Place');
    if (!$this->entity->isNew()) {
      $button_value = $this->t('Update');
    }

    // Override normal EmbedParagraphForm actions as we need to be AJAX
    // compatible, and also need to communicate with our App.
    $actions['submit'] = [
      '#type' => 'button',
      '#value' => $button_value,
      '#name' => 'paragraphs_entity_embed_submit',
      '#ajax' => [
        'callback' => '::submitForm',
        'wrapper' => 'paragraphs-entity-embed-type-form-wrapper',
        'method' => 'replace',
        'progress' => [
          'type' => 'throbber',
          'message' => '',
        ],
      ],
    ];

    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $storage = $form_state->getStorage();
    $editor = $storage['paragraph_embed_dialog']['editor'];
    $embed_button = $storage['paragraph_embed_dialog']['embed_button'];
    unset($form['field_teaser_text']);
    unset($form['field_teaser_media']);
    //kint($form);
//    kint(array_keys($storage));
//    kint($storage['paragraph_embed_dialog']);
//    kint($storage['paragraph_embed_dialog']['field_id']);
//    kint($form);
    if (isset($storage['paragraph_embed_dialog']['field_id'])) {
      $field = $storage['paragraph_embed_dialog']['field_id'];
      $form_field = $form[$field];
      $form = ['field_id' => $form_field];
    }
    kint($form);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
