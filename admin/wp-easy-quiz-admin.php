<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

class wp_easy_quiz_quiz_admin {
	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name;
	/**
	 * Plugin version.
	 *
	 * @var int
	 */
	protected $plugin_version;
	/**
	 * Basic constructor. Invoke hooks.
	 *
	 * @param string $plugin_name       name of the plugin.
	 * @param string $plugin_version version of the plugin.
	 */
	function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name    = $plugin_name;
		$this->plugin_version = $plugin_version;

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'init', array( $this,'reg_quiz_post_type' ) );
		//add_action( 'admin_menu', array( $this,'admin_menu_wpmr_page' ) );
		add_action( 'add_meta_boxes', array( $this, 'reg_quiz_post_meta_box' ) );
		add_action('wp_ajax_wp_easy_quiz_ajax_call', array( $this, 'wp_easy_quiz_ajax_call' ));
		add_action ( 'admin_enqueue_scripts', array( $this, 'add_media' ));
	}

	/**
	 * Register scripts
	 */
	public function register_scripts() {
		// Enqueue styles
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ).'css/admin_style.css', array(), $this->plugin_version, 'all' );
		wp_enqueue_style( $this->plugin_name );

		// Enqueue js
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ).'js/admin_script.js', array( 'jquery' ), $this->plugin_version, 'all' );
		wp_enqueue_script( $this->plugin_name );
		wp_localize_script( $this->plugin_name, 'make_call', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'magizhchi' )
		));
	}

	/**
	 * Register post_type.
	 */
	public function reg_quiz_post_type() {
		$singular_name = 'Quiz';
		$plural_name   = 'Quizzes';
		$text_domain   = 'wpeasyquiz';
		$menu_name     = 'Wp Easy Quiz';

		$labels = array(
			'name'               => _x($plural_nale, $text_domain),
			'singular_name'      => _x($singular_name, $text_domain),
			'add_new'            => _x('Add New', $text_domain),
			'add_new_item'       => __('Add New '.$singular_name, $text_domain),
			'edit_item'          => __('Edit '.$singular_name, $text_domain),
			'new_item'           => __('New '.$singular_name, $text_domain),
			'all_items'          => __('All '.$plural_name, $text_domain),
			'view_item'          => __('View '.$singular_name, $text_domain),
			'search_items'       => __('Search '.$plural_name, $text_domain),
			'not_found'          => __('No '.$plural_name.' found', $text_domain),
			'not_found_in_trash' => __('No '.$plural_name.' found in Trash', $text_domain),
			'parent_item_colon'  => '',
			'menu_name'          => __($menu_name, $text_domain),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-admin-post',
			'supports'           => array( 'title' ),
		);
		register_post_type( 'wp_easy_quiz', $args );
	}

	/**
	 * Register post meta.
	 */
	public function reg_quiz_post_meta_box() {
		$post_types = 'wp_easy_quiz';
		if( get_post_type() == $post_types ) {
			add_meta_box(
				$post_types
				,__( 'Wp Easy Quiz', 'wpeasyquiz' )
				,array( $this, 'meta_box_content' )
				,$wpr_posttypes
				,'advanced'
				,'default'
			);
		}
	}

	/**
	 * Meta box
	 */
	public function meta_box_content( $post ) {
		?>
		<div class="wp-easy-quiz-wrap">
			<h3>Questions</h3>
			<div class="add-quiz-qstn base-qp" data-toggel="close">
				<input type="hidden" id="post-id" value="<?php echo $post->ID;?>"> <!--Post ID-->
				<div class="wpeq-row">
					<div class="wpeq-col-6">
						<label>Title</label>
						<input type="text" id="question-0" class="input-control">
					</div>
					<div class="wpeq-col-6">
						<label>Image or Video URL</label>
						<div class="meta-upload">
							<input type="text" id="media-0" class="input-control">
							<button type="button" data-meta="0" class="m_upload wpeq-btn">Upload</button>
						</div>
					</div>
				</div>
			</div>
			<div class="wpeq-btn-sec">
				<button type="button" data-meta="0" class="add-quiz wpeq-btn">Add Question</button>
				<button type="button" data-meta="0" class="cancel-quiz wpeq-btn">Cancel</button>
			</div>
			<div class="added-quiz-qstn">
				<?php
					$postid = get_the_ID();
					$this->meta_loop( $postid );
				?>
			</div>
		</div>
		<?php
	}

	// Include media js
	public function add_media() {
		if (is_admin ())
		wp_enqueue_media ();
	}

	// Qiz form loop
	public function meta_loop( $postid ) {
		if ( $postid ) {
			$get_meta = get_post_meta( $postid, '_wpeq_question', true );
			if ( $get_meta && is_array($get_meta) ) {
				foreach ( $get_meta as $k=>$meta ) { $k = $k+1;
					?>
					<div class="quiz-panel" id="quiz-panel-<?php echo $k;?>">
						<h3 class="wpeq-title"><?php echo $meta['title'];?></h3>
						<div class="wpeq-action-btns">
							<span class="dashicons dashicons-arrow-down wpeq-expand" data-meta="<?php echo $k;?>" data-toggle="#expand-panel-<?php echo $k;?>"></span>
							<span data-row_delete="#quiz-panel-<?php echo $k;?>" class="dashicons dashicons-trash" data-meta="<?php echo $k;?>"></span>
						</div>
						<div id="expand-panel-<?php echo $k;?>" class="add-quiz-qstn" data-expand="false">
							<div class="wpeq-row">
								<div class="wpeq-col-6">
									<label>Title</label>
									<input type="text" id="question-<?php echo $k;?>" class="input-control" value="<?php echo $meta['title'];?>">
								</div>
								<div class="wpeq-col-6">
									<label>Image or Video URL</label>
									<div class="meta-upload">
										<input type="text" id="media-<?php echo $k;?>" class="input-control" value="<?php echo $meta['media'];?>">
										<button type="button" data-meta="<?php echo $k;?>" class="m_upload wpeq-btn">Upload</button>
									</div>
								</div>
							</div>
							<!--Quiz answers-->
							<?php
								$options = $meta['options'];
								$this->answer_option( $options, $k );
							?>
							<div class="wpeq-btn-sec">
								<button type="button" data-meta="<?php echo $k;?>" class="save-quiz wpeq-btn">Save Changes</button>
							</div>
						</div>
					</div>
					<?php
				}
			}
		}
	}

	// Answer option loop
	public function answer_option( $options, $k ) {
		?>
		<div id="ans-panel-<?php echo $k;?>" class="ans-panel">
			<h3>Answer options</h3>
			<div class="wpeq-row">
				<div class="wpeq-col-6">
					<input type="text" id="add-optn-<?php echo $k;?>" class="input-control">
				</div>
				<div class="wpeq-col-6">
					<input type="checkbox" id="add-optn-true-<?php echo $k;?>" value="yes">
					<button type="button" data-meta="<?php echo $k;?>" class="wpeq-btn option-plus">Add</button>
				</div>
			</div>
			<div id="option-panel-<?php echo $k;?>">
				<div class="wpeq-row">
					<?php
						if ( $options && is_array($options) ) {
							foreach ( $options as $optn_key=>$option ) {
								?>
								<div id="ans-option-<?php echo $optn_key;?>" class="wpeq-col-6">
									<div class="ans-option">
										<div class="first-field">
											<input type="text" id="option-<?php echo $optn_key;?>" class="input-control ans-option-<?php echo $k;?>" value="<?php echo $option['option'];?>">
										</div>
										<div class="last-field">
											<input type="checkbox" class="optn-true-<?php echo $k;?>" value="yes" <?php echo $option['select_ans'];?>>
											<?php if ( count($options) > 1 ) { ?>
												<span data-delete_optn="<?php echo $optn_key;?>" class="dashicons dashicons-trash"></span>
											<?php }?>
										</div>
									</div>
								</div>
								<?php
							}
						}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	// Save quiz content
	public function save_quiz_by_id( $postid, $key ) {
		$get_meta = get_post_meta( $postid, '_wpeq_question', true );
		$k = $key = $key;
		$title = $get_meta[$key]['title'];
		$media = $get_meta[$key]['media'];
		$options = $get_meta[$key]['options'];
		?>

		<h3 class="wpeq-title"><?php echo $title;?></h3>
		<div class="wpeq-action-btns">
			<span class="dashicons dashicons-arrow-down wpeq-expand" data-meta="<?php echo $key+1;?>" data-toggle="#expand-panel-<?php echo $key+1;?>"></span>
			<span data-row_delete="#quiz-panel-<?php echo $key+1;?>" class="dashicons dashicons-trash" data-meta="<?php echo $key+1;?>"></span>
		</div>
		<div id="expand-panel-<?php echo $key+1;?>" class="add-quiz-qstn" data-expand="true">
			<div class="wpeq-row">
				<div class="wpeq-col-6">
					<label>Title</label>
					<input type="text" id="question-<?php echo $key+1;?>" class="input-control" value="<?php echo $title;?>">
				</div>
				<div class="wpeq-col-6">
					<label>Image or Video URL</label>
					<div class="meta-upload">
						<input type="text" id="media-<?php echo $key+1;?>" class="input-control" value="<?php echo $media;?>">
						<button type="button" data-meta="<?php echo $key+1;?>" class="m_upload wpeq-btn">Upload</button>
					</div>
				</div>
			</div>
			<!--Quiz answers-->
			<?php $k = $key+1; $this->answer_option( $options, $k );?>
			<div class="wpeq-btn-sec">
				<button type="button" data-meta="<?php echo $key+1;?>" class="save-quiz wpeq-btn">Save Changes</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Ajax Call
	 */
	public function wp_easy_quiz_ajax_call() {
		$nonce = $_POST['nonce'];
		if ( ! isset($nonce) ) {
			die( 'Not a valid user !' );
		}

		if ( ! wp_verify_nonce( $nonce, 'magizhchi' ) ) {
			die( 'Not a valid user !' );
		}

		// Add quiz question
		if ( $_POST['mode'] == 'add_qustn' ) {
			$postid      = intval( $_POST['postid'] );
			$question    = sanitize_text_field( $_POST['question'] );
			$media       = esc_url( $_POST['media'] );
			$update_meta = '';
			$value    = array(
				'title' => $question,
				'media' => $media,
				'options' => ''
			);

			$get_meta = get_post_meta( $postid, '_wpeq_question', true );
			if ( $get_meta && is_array($get_meta) ) {
				$get_meta[] = $value;
				$update_meta = update_post_meta( $postid, '_wpeq_question', $get_meta );
			} else {
				$get_meta    = array();
				$get_meta[]  = $value;
				$update_meta = update_post_meta( $postid, '_wpeq_question', $get_meta );
			}

			// Response data
			if ( $update_meta ) {
				$this->meta_loop( $postid );
			}
		}

		// Media url
		if ( $_POST['mode'] == 'attchment' ) {
			echo wp_get_attachment_url( $_POST['media_id'] );
		}

		// Save the quiz
		if ( $_POST['mode'] == 'update_qustn' ) {
			$postid   = intval( $_POST['postid'] );
			$question = sanitize_text_field( $_POST['question'] );
			$media    = esc_url( $_POST['media'] );
			$options  = $_POST['options'];
			$key      = $_POST['meta_key']-1;

			$get_meta                  = get_post_meta( $postid, '_wpeq_question', true );
			$get_meta[$key]['title']   = $question;
			$get_meta[$key]['media']   = $media;
			if ( is_array( $options ) ) {
				$get_meta[$key]['options'] = $options;
			}
			$update_meta               = update_post_meta( $postid, '_wpeq_question', $get_meta );
			$this->save_quiz_by_id( $postid, $key );
		}
		die();
	}
}