<?php
/*
Plugin Name: Wp Easy Quiz
Plugin URI:  https://wordpress.org/plugins/wp-easy-quiz/
Description: Create quiz
Author:      Anandaraj balu
Version:     1.0
Author URI:  https://profiles.wordpress.org/anand000
Text Domain: wpeasyquiz
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Copyright 2018 Anandaraj balu (email: anandrajbalu00@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

require_once plugin_dir_path( __FILE__ ) . 'includes/wp-easy-quiz-base.php';

function wp_easy_quiz() {
	$plugin = new wp_easy_quiz_base();
}
wp_easy_quiz();