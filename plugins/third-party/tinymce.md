---
layout: plugin-nav-bar
group: third-party
---

# UI.Tinymce directive

This [directive](https://github.com/Wizehive/ui-tinymce) allows you to add a TinyMCE editor to your form elements.

## Usage

Apply the directive to your form elements:

{% highlight html%}
<form method="post">
  <textarea ui-tinymce ng-model="tinymceModel"></textarea>
</form>
{% endhighlight %}

**Be sure not to set an `id` attribute**. This is because the directive needs to maintain selector knowledge in order to handle buggy behavior in TinyMCE when DOM manipulation is involved, such as in a reordering of HTML through ng-repeat or DOM destruction/recreation through ng-if.

## Working with ng-model

The ui-tinymce directive plays nicely with the ng-model directive such as ng-required.

If you add the ng-model directive to same the element as ui-tinymce then the text in the editor is automatically synchronized with the model value.

The ui-tinymce directive stores the configuration options as specified in the [TinyMCE documentation](http://www.tinymce.com/wiki.php/Configuration) and expects the model value to be a html string or raw text, depending on whether `raw` is `true` (default value is `false`).

## Options

The directive supports all of the standard TinyMCE initialization options as listed [here](http://www.tinymce.com/wiki.php/Configuration).

In addition, it supports these additional optional options

- `format` Format to get content as, i.e. `raw` for raw HTML, or `text` for text only. Documentation [here](http://www.tinymce.com/wiki.php/api4:method.tinymce.Editor.getContent).
- `trusted` When `true`, all TinyMCE content that is set to `ngModel` will be whitelisted by `$sce`.
- `mode` Preset options for toolbar and plugin controls. **We strongly encourage you to use one of these preset modes so that users of your plugin will have a consistent experience with other editors in the app.**
Two modes are available: `basic` or `advanced`. If a mode is not set it will default to `basic`. Check the table below for a complete list of toolbar controls set for each mode.
If you wish to customize the set of plugins/toolbars, do not specify a `mode`. You may use the listings below as a baseline for your options.

<table class="table">
	<thead>
		<tr>
			<th>Mode</th>
			<th>Toolbar controls</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<p>basic</p>
			</td>
			<td>
				<p>plugins: &quot;link textcolor hr code&quot;<br>
toolbar: &quot;bold italic | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | hr link&quot;</p>
			</td>
		</tr>
		<tr>
			<td>
				<p>advanced</p>
			</td>
			<td>
				<p>plugins: &quot;link textcolor hr table code image paste searchreplace fullscreen&quot;<br>
toolbar: &quot;styleselect fontselect | bold italic | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | hr link image table | searchreplace | fullscreen code&quot;</p>
			</td>
		</tr>
	</tbody>
</table>

{% highlight js%}
myAppModule.controller('MyController', function($scope) {
  $scope.tinymceOptions = {
    onChange: function(e) {
      // put logic here for keypress and cut/paste changes
    },
    inline: false,
    plugins : 'advlist autolink link image lists charmap print preview',
    toolbar: 'bold italic | forecolor backcolor | alignleft aligncenter alignright | print preview'
  };
});
{% endhighlight %}

{% highlight html%}
<form method="post">
  <textarea ui-tinymce="tinymceOptions" ng-model="tinymceModel"></textarea>
</form>
{% endhighlight %}

## Troubleshooting

If you are conditionally hiding/showing the TinyMCE editor you may run into an issue where the editor's contents aren't updating to reflect `ng-model` changes. In that case you can fix it by calling:

{% highlight js%}
$scope.$broadcast('$tinymce:refresh');
{% endhighlight %}
