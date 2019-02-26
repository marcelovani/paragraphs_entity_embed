<?php

namespace Drupal\paragraphs_entity_embed\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs_entity_embed\EmbeddedParagraphsForm;

/**
 * Provides a form to embed paragraphs.
 */
class ParagraphEmbedDialog extends EmbeddedParagraphsForm {

  const INSERT_METHOD_PROMPTED = 1;

  const INSERT_METHOD_SELECTED = 2;

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {

    $button_value = $this->t('Create and Place');
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
    $editor = $storage['editorParams']['editor'];
    $embed_button = $storage['editorParams']['embed_button'];

    $form['is_new'] = [
      '#type' => 'value',
      '#value' => $this->entity->isNew(),
    ];

    $form['attributes']['#tree'] = TRUE;
    $form['attributes']['data-embed-button'] = [
      '#type' => 'value',
      '#value' => $embed_button->id(),
    ];

    $form['#attached']['library'][] = 'editor/drupal.editor.dialog';
    $form['#attached']['library'][] = 'paragraphs_entity_embed/dialog';
    // Wrap our form so that our submit callback can re-render the form.
    $form['#prefix'] = '<div id="paragraphs-entity-embed-type-form-wrapper">';
    $form['#suffix'] = '</div>';

    // On inserting new paragraph allow administrator to choose whether new
    // paragraph should be created or an existing one will be used.
    if (
      // Conditions: entity should be new.
      $this->entity->isNew() &&
      // Insert method should not been chosen yet.
      $form_state->get('insert_method_choise') !== self::INSERT_METHOD_SELECTED
    ) {
      // Raise a flag that method needs to be selected.
      $form_state->set('insert_method_choise', self::INSERT_METHOD_PROMPTED);
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $triggering_element = $form_state->getTriggeringElement();

    // If we have chosen to insert new paragraph we raise a flag that the insert
    // method has been chosen and show the form.
    if ($triggering_element['#name'] === 'create_new_paragraphs_entity_embed') {
      $form_state->set('insert_method_choise', self::INSERT_METHOD_SELECTED);
    }

    // If we have chosen to use existing paragraph we check if the value is not
    // empty.
    if (
      $triggering_element['#name'] === 'use_existing_paragraphs_entity' &&
      !$form_state->getValue('existing')['autocomplete'] &&
      !$form_state->getErrors()
    ) {
      $form_state->setErrorByName(
        'existing][autocomplete',
        $this->t('@name field is required.', [
          '@name' => $form['existing']['autocomplete']['#title'],
        ])
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();

    // Return early if there are any errors or if a button we're not aware of
    // submitted the form.
    if (
      $form_state->hasAnyErrors() ||
      $triggering_element['#name'] !== 'paragraphs_entity_embed_submit'
    ) {
      return $form;
    }

    // Submit the parent form and save. This mimics the normal behavior of the
    // submit element in our parent form(s).
    parent::submitForm($form, $form_state);
    $this->save($form, $form_state);

    return (new AjaxResponse())
      ->addCommand(
        new InvokeCommand(
          NULL,
          'ParagraphEditorDialogSaveAndCloseModalDialog',
          [
            $form_state->getValues()['attributes'] +
            ['data-paragraph-id' => $this->entity->uuid()],
          ]
        )
      );
  }
}
