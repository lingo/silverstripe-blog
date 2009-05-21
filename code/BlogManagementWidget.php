<?php
/**
 * Blog Management Widget
 * @package blog
 */
class BlogManagementWidget extends Widget implements PermissionProvider {

	static $db = array();

	static $has_one = array();

	static $has_many = array();

	static $many_many = array();

	static $belongs_many_many = array();

	static $defaults = array();

	static $title = "Blog Management";
	static $cmsTitle = "Blog Management";
	static $description = "Provide a number of links useful for administering a blog. Only shown if the user is an admin.";

	function CommentText() {
		$unmoderatedcount = DB::query("SELECT COUNT(*) FROM PageComment WHERE NeedsModeration=1")->value();
		if($unmoderatedcount == 1) {
			return _t("BlogManagementWidget.UNM1", "You have 1 unmoderated comment");
		} else if($unmoderatedcount > 1) {
			return sprintf(_t("BlogManagementWidget.UNMM", "You have %i unmoderated comments"), $unmoderatedcount);
		} else {
			return _t("BlogManagementWidget.COMADM", "Comment administration");
		}
	}

	function CommentLink() {
		if(!Permission::check('ADMIN')) {
			return false;
		}
		$unmoderatedcount = DB::query("SELECT COUNT(*) FROM PageComment WHERE NeedsModeration=1")->value();

		if($unmoderatedcount > 0) {
			return "admin/comments/unmoderated";
		} else {
			return "admin/comments";
		}
	}

	function providePermissions() {
		return array("BLOGMANAGEMENTWIDGET_VIEW" => "View blog management widget");
	}

	function WidgetHolder() {
		if(Permission::check("BLOGMANAGEMENTWIDGET_VIEW")) {
			return $this->renderWith("WidgetHolder");
		}
	}

	function PostLink() {
		$container = BlogTree::current();
		if ($container) return $container->Link('post');
	}

	function getBlogHolder() {
		$page = Director::currentPage();

		if($page->is_a("BlogHolder")) {
			return $page;
		} else if($page->is_a("BlogEntry") && $page->getParent()->is_a("BlogHolder")) {
			return $page->getParent();
		} else {
			return DataObject::get_one("BlogHolder");
		}
	}
}

?>