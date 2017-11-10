---
layout: plugin-nav-bar
group: third-party
---

# Ng-Showdown directive

This [directive](https://github.com/showdownjs/ng-showdown){:target="_blank"} allows you to render markdown.

## Usage

You can convert markdown to html within your controller.

{% highlight js%}
plugin.controller('MyController', function($scope) {

		$scope.markdownSource = "### H3\nThis is **important**!";

		$scope.renderedHTML = $showdown.makeHtml($scope.markdownSource);

});
{% endhighlight %}

Apply the directive to your templates:

{% highlight html%}
<div markdown-to-html="'{{"{{markdownSource"}}}}'"></div>
{% endhighlight %}


## Markdown

### Supported Features

<a href="#headers">Headers</a>

<a href="#emphasis">Emphasis</a>

<a href="#lists">Lists</a>

<a href="#links">Links</a>

<a href="#images">Images</a>

<a href="#hrules">Horizontal Rules</a>

<a href="#linebreaks">Line Breaks</a>

---

<div id="headers"></div>

#### Headers

---

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
## H1
---
### H1
---
#### H1
---
##### H1
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

You can define a link including link text as:

```
[Click here to Search](https://www.google.com)
```

[Click here to Search](https://www.google.com)

You can also simply use the link directly as:

```
http://www.example.com
```

<http://www.example.com>

---

<div id="images"></div>

#### Images

You can define a in line image as:

```
Here's our logo (hover to see the title text):

![alt text](https://www.wizehive.com/hubfs/01-Zenginehq-Aug2016/logo.png "Logo Title Text 1")
```

Here's our logo (hover to see the title text):

Inline-style:
![alt text](https://www.wizehive.com/hubfs/01-Zenginehq-Aug2016/logo.png "Logo Title Text 1")

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
