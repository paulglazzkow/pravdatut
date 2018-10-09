<?php

namespace Drupal\block_login_social\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_auth\Plugin\Block\SocialAuthLoginBlock;
use Drupal\user\Plugin\Block\UserLoginBlock;

/**
 * Provides a 'BlockLoginSocial' block.
 *
 * @Block(
 *  id = "block_login_social",
 *  admin_label = @Translation("Login Social block"),
 * )
 */
class BlockLoginSocial extends BlockBase {

  //  use RedirectDestinationTrait;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;


  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    //    $build['block_login_social_social_media']['#markup'] = '<p>' . $this->configuration['social_media'] . '</p>';
    /* @var $plugins \Drupal\Core\Block\BlockManager */
    $plugins = \Drupal::service('plugin.manager.block');

    $login_definition = $plugins->getDefinition('user_login_block');
    $login_block = new UserLoginBlock([], 'user_login_block', $login_definition, \Drupal::service('current_route_match'));

    $social_definition = $plugins->getDefinition('social_auth_login');
    $social_block = new SocialAuthLoginBlock(
      [],
      'social_auth_login',
      $social_definition,
      \Drupal::config('social_auth.settings')
    );

    $build['block_login_social_social_media'] = [
      '#type' => 'container',
    ];

    $build['block_login_social_social_media']['login'] = [
      '#type' => 'fieldgroup',
      '#title' => 'Login',
      'content' => $login_block->build(),
    ];

    $social_block = $social_block->build();
    $social_block['#theme'] = 'login_social_buttons';
    $build['block_login_social_social_media']['social'] = [
      '#type' => 'fieldgroup',
      '#title' => 'Social',
      'block' => $social_block,
    ];


    // \Drupal::formBuilder()->getForm('Drupal\social_login\Form\SocialLoginBlock');
    //    foreach ($blocks as &$block) {
    //      $block['storage'] = \Drupal::entityTypeManager()
    //        ->getStorage('block')
    //        ->loadMultiple();
    //      //      $block['content'] = \Drupal::entityTypeManager()->getViewBuilder('block_content')->view($block['storage']);
    //    }

    return $build;
  }

}
