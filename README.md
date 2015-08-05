# Cherry Team
A team management plugin for WordPress.
Сompatibility: *Cherry Framewokr v.4+*

##Change log##

#### v1.0.2 ####

* UPD: Optimize a shortcode registration
* UPD: Optimize conditional rule for a `pre_get_posts` filter
* UPD: Text to translate
* FIX: archive-team.php file

#### v1.0.1 ####

* ADD: compatibility for wpml-plugin
* DEL: `cherry_display_sidebar` filter
* UPD: Updater logic

## Features
* CPT Team
* Page template (named Team) + single template
* Widget (named Cherry Team Widget)
* Shortcode (named cherry_team)
* 20+ custom hooks
* Translation (Localization)

## How to use

#### In a post/page
Insert a shortcode `[cherry_team]` to the post/page content.

#### In a page template
Fire the action *'cherry_get_team'*. Example:
```
	do_action( 'cherry_get_team' );
```

#### In a sidebar
Just drop widget to the your sidebar.

## Help
Found a bug? Feature requests? [Create an issue - Thanks!](https://github.com/CherryFramework/cherry-team/issues/new)