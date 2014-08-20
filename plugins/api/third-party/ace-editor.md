---
layout: plugin-nav-bar
group: api
subgroup: ace-editor
---

# UI.Ace directive [![Build Status](https://travis-ci.org/angular-ui/ui-ace.png)](https://travis-ci.org/angular-ui/ui-ace)

This [directive](https://github.com/Wizehive/ui-ace) allows you to add [ACE](http://ajaxorg.github.io/ace/) editor elements.


## Usage

Add the directive to your html:

{% highlight html %}
<div ui-ace></div>
{% endhighlight %}

To see something it's better to add some CSS, like


{% highlight css %}
.ace_editor { height: 200px; }
{% endhighlight %}


## Options

Ace doesn't provide a one gate access to all the options the jquery way.
Each option is configured with the function of a specific instance.
See the [api doc](http://ajaxorg.github.io/ace/#nav=api) for more.

Although, _ui-ace_ automatically handles some handy options :
 + _showGutter_ : to show the gutter or not.
 + _useWrapMode_ : to set whether or not line wrapping is enabled.
 + _theme_ : to set the theme to use.
 + _mode_ : to set the mode to use.
 + _onLoad_ : callback when the editor has finished loading (see [below](#ace-instance-direct-access)).
 + _onChange_ : callback when the editor content is changed ().

{% highlight html %}
<div ui-ace="{
    useWrapMode : true,
    showGutter: false,
    theme:'twilight',
    mode: 'xml',
    onLoad: aceLoaded,
    onChange: aceChanged
}"></div>
{% endhighlight %}

You'll want to define the `onLoad` and the `onChange` callback on your scope:

{% highlight js %}
plugin.controller('MyController', [ '$scope', function($scope) {

    $scope.aceLoaded = function(_editor) {
        // Options
        _editor.setReadOnly(true);
    };

    $scope.aceChanged = function(e) {
        //
    };

}]);
{% endhighlight %}

To handle other options you'll have to use a direct access to the Ace created instance (see [below](#ace-instance-direct-access)).

### Working with ng-model

The ui-ace directive plays nicely with ng-model.

The ng-model will be watched for to set the Ace EditSession value (by [setValue](http://ajaxorg.github.io/ace/#nav=api&api=edit_session)).

_The ui-ace directive stores and expects the model value to be a standard javascript String._

### Can be read only

Simple demo
{% highlight html %}
<div ui-ace readonly="true"></div>
or
Check me to make Ace readonly: <input type="checkbox" ng-model="checked" ><br/>
<div ui-ace readonly="{{checked}}"></div>
{% endhighlight %}

### Ace instance direct access

For more interaction with the Ace instance in the directive, we provide a direct access to it.
Using

{% highlight html %}
<div ui-ace="{ onLoad : aceLoaded }" ></div>
{% endhighlight %}

the `$scope.aceLoaded` function will be called with the [Ace Editor instance](http://ajaxorg.github.io/ace/#nav=api&api=editor) as first argument

{% highlight js %}
plugin.controller('MyController', [ '$scope', function($scope) {

    $scope.aceLoaded = function(_editor){
        // Editor part
        var _session = _editor.getSession();
        var _renderer = _editor.renderer;

        // Options
        _editor.setReadOnly(true);
        _session.setUndoManager(new ace.UndoManager());
        _renderer.setShowGutter(false);

        // Events
        _editor.on("changeSession", function(){ ... });
        _session.on("change", function(){ ... });
    };

}]);
{% endhighlight %}
