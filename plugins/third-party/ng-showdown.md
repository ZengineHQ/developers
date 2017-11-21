---
layout: plugin-nav-bar
group: third-party
---

# Ng-Showdown directive

This [directive](https://github.com/showdownjs/ng-showdown){:target="_blank"} allows you to render markdown.

## Usage

You can convert markdown to html within your controller.

{% highlight js%}
plugin.controller('MyController', function($scope, $filter) {

		$scope.markdownSource = "### H3\nThis is **important**!";

		$scope.renderedHTML = $showdown.makeHtml($showdown.stripHtml($scope.markdownSource));

});
{% endhighlight %}

Apply the directive to your templates:

{% highlight html%}
<div markdown-to-html="markdownSource | stripHtml"></div>
{% endhighlight %}


## znMarkdown filter

For compatibility, most plugins should take advantage of the **_znMarkdown_** filter. It strips input HTML tags, sanitizes unsafe links and images, and then renders markdown.

You can use it within your controller.

{% highlight js%}
plugin.controller('MyController', function($scope, $filter) {

		var $znMarkdown = $filter('znMarkdown');

		$scope.markdownSource = "### H3\nThis is **important**!";

		$scope.renderedHTML = $znMarkdown($scope.markdownSource);

});
{% endhighlight %}

Apply the directive to your templates:

{% highlight html%}
<div ng-bind-html="markdownSource | znMarkdown"></div>
{% endhighlight %}

## znPresentationalText filter

There is an additional convenience filter for presentation text fields:  **znPresentationalText_** filter. It will conditionally process markdown for field or yield it's plain text label.

You can use it within your controller.

{% highlight js%}
plugin.controller('MyController', function($scope, $filter) {

		var $znPresentationalText = $filter('znPresentationalText');

		$scope.field = { ... };

		$scope.renderedHTML = $znPresentationalText($scope.field);

});
{% endhighlight %}


## Markdown

### Supported Features

<a href="#headers">Headers</a>

<a href="#emphasis">Emphasis</a>

<a href="#lists">Lists</a>

<a href="#links">Links</a>

<a href="#codeblocks">Code Blocks</a>

<a href="#blockquotes">Blockquotes</a>

<a href="#hrules">Horizontal Rules</a>

<a href="#linebreaks">Line Breaks</a>

### Unsupported Features

<a href="#images">Images</a>

<a href="#tables">Tables</a>

<a href="#inlinehtml">Inline HTML</a>

<div id="headers"></div>

---

<div id="headers"></div>

#### Headers

```
# H1
## H2
### H3
#### H4
##### H5
###### H6

Alternatively, for H1 and H2, an underline-ish style:

Alt-H1
======

Alt-H2
------
```

---
# H1
---
## H2
---
### H3
---
#### H4
---
##### H5
---
###### H6
---

Alternatively, for H1 and H2, an underline-ish style:

Alt-H1
======

---

Alt-H2
------

---

<div id="emphasis"></div>

#### Emphasis (bold,italic,strikethrough)

```
Emphasis, aka italics, with *asterisks* or _underscores_.

Strong emphasis, aka bold, with **asterisks** or __underscores__.

Combined emphasis with **asterisks and _underscores_**.

Strikethrough uses two tildes. ~~Scratch this.~~
```

Emphasis, aka italics, with *asterisks* or _underscores_.

Strong emphasis, aka bold, with **asterisks** or __underscores__.

Combined emphasis with **asterisks and _underscores_**.

Strikethrough uses two tildes. ~~Scratch this.~~

---

<div id="lists"></div>

#### Lists

```
1. First ordered list item
2. Another item
  * Unordered sub-list.
1. Actual numbers don't matter, just that it's a number
  1. Ordered sub-list
4. And another item.

   Some text that should be aligned with the above item.

* Unordered list can use asterisks
- Or minuses
+ Or pluses
```

1. First ordered list item
2. Another item
  * Unordered sub-list.
1. Actual numbers don't matter, just that it's a number
  1. Ordered sub-list
4. And another item.

   Some text that should be aligned with the above item.

* Unordered list can use asterisks
- Or minuses
+ Or pluses

---

<div id="links"></div>

#### Links

**_Note: only secure "https://" links are allowed.  Non-secure links will be ignored._**

You can define a link including link text as:

```
[Click here to Search](https://www.google.com)
```

[Click here to Search](https://www.google.com)

You can also simply use the link directly as:

```
https://www.example.com
```

<https://www.example.com>

<div id="codeblocks"></div>

---

## Code Blocks

```
Inline `code` has `back-ticks around` it.
```

Inline `code` has `back-ticks around` it.

Or fenced:

```
` ` `
code
` ` `
```

```
code
```

<div id="blockquotes"></div>

---

## Blockquote

```
> Blockquotes are very handy in email to emulate reply text.
> This line is part of the same quote.

Quote break.

> This is a very long line that will still be quoted properly when it wraps. Oh boy let's keep writing to make sure this is long enough to actually wrap for everyone. Oh, you can *put* **Markdown** into a blockquote.
```

> Blockquotes are very handy in email to emulate reply text.
> This line is part of the same quote.

Quote break.

> This is a very long line that will still be quoted properly when it wraps. Oh boy let's keep writing to make sure this is long enough to actually wrap for everyone. Oh, you can *put* **Markdown** into a blockquote.

---

<div id="hrules"></div>

#### Horizontal Rules

```
Three or more...

---

Hyphens

***

Asterisks

___

Underscores
```

Three or more...

---

Hyphens

***

Asterisks

___

Underscores

---

<div id="linebreaks"></div>

#### Line Breaks

```
Here is a line for us to start with.

This line is separated from the one above by two newlines, so it will be a *separate paragraph*.

This line is also a separate paragraph, but...
This line is only separated by a single newline, so it is a separate line in the *same paragraph*.
```
Here's a line for us to start with.

This line is separated from the one above by two newlines, so it will be a *separate paragraph*.

This line is also a separate paragraph, but...
This line is only separated by a single newline, so it's a separate line in the *same paragraph*.


---

# Images

**_Note: Image links are not secure and will be ignored._**

```
![alt text](http://www.example.com/logo.png "Logo Title Text 1")
```


<div style="all: unset;font-style: oblique">
![alt text](http://www.example.com/logo.png "Logo Title Text 1")
</div>

<div id="tables"></div>

---

## Tables

**_Note: Tables are not supported and will be ignored._**

```
| Tables        | Are           | Cool  |
| ------------- |:-------------:| -----:|
| col 3 is      | right-aligned | $1600 |
| col 2 is      | centered      |   $12 |
| zebra stripes | are neat      |    $1 |
```

<div style="all: unset;font-style: oblique">
| Tables        | Are           | Cool  |
<br />
| ------------- |:-------------:| -----:|
<br />
| col 3 is      | right-aligned | $1600 |
<br />
| col 2 is      | centered      |   $12 |
<br />
| zebra stripes | are neat      |    $1 |
</div>

<div id="inlinehtml"></div>

---

## Inline HTML

**_Note: Inline HTML is not supported and will be ignored._**

```
<div style="color: red;">Just the text left.</div>
```

<div style="all: unset;font-style: oblique">
Just the text left.
</div>

---