---
layout: nav-bar
group: conventions
subgroup: data-filters
---

# Conventions

## Data Filters

Data filters are JSON objects defining a set of matching rules for Form Records.

The top level of a filter is an object with one key-value pair.  The key is an operator (`and` / `or`) and the value is an array of filtering condition objects.  Following standard boolean logic, an `and` operator will result in a set of records that match every condition, while an `or` operator will result in a set of records that match any one or more conditions.  The operator is required even if there is only one condition.

Example format:
{% highlight js%}
{
  "and": [
    {"prefix": "", "attribute": "field123", "value": "value1"},
    {"prefix": "not", "attribute": "field456", "value": "value1"}
  ]
}
{% endhighlight %}

### Conditions
Each condition object contains three keys: `prefix`, `attribute`, and either `value` or `filter`.

The `prefix` specifies the mode for matching.  See the Prefixes section for more information.

The `attribute` specifies the data field to check for matching.  See the Attributes section for more information.

If `value` is passed, records with attributes matching the `prefix` and `value` will be produced for this condition.  This is the most common condition type.  `value` is passed as a string or number.

Optionally, a filter object may be passed to the `filter` key instead of a `value`.  This allows you to set up a subfilter on linked forms.  See the Subfilters section for more information.  

### Prefixes
One `prefix` is required for every condition. A `prefix` is passed as a string.  Note that some prefixes are applicable only to certain data types.

* '' (empty string): Equals
* '__not__': Does not equal
* '__min__': Minimum, inclusive (numeric/date)
* '__max__': Maximum, inclusive (numeric/date)
* '__not-contains__': Does not contain substring
* '__contains__': Contains substring
* '__starts-with__': Starts with substring
* '__ends-with__': Ends with substring

### Attributes
An `attribute` may be either a form field or a basic property of a record.  Inspect the results of a request to the form records endpoint to see attributes you can choose from.  If your record comes back with 'field123' and 'field456' keys, those may be used as attributes.

Likewise you can use standard attributes on records such as `created`, `modified`, `id`, and `name`.

You cannot filter on related non-record objects such as activities, events, or tasks.  However you can pass a numeric id to the special `folder.id` or `createdByUser.id` attributes.

### Complex Queries

It is possible to create more complex queries combining both `and` and `or` by nesting additional operators and rules as conditions. There is a 5 level limit of nested conditions.

Example format:
{% highlight js%}
{
  "and": [
    {"prefix": "", "attribute": "field1", "value": "value1"},
    {
      "or": [
        {"prefix": "", "attribute": "field2", "value": "value2"},
        {"prefix": "", "attribute": "field3", "value": "value3"},
        {
          "and": [
            {"prefix": "", "attribute": "field4", "value": "value4"},
            {"prefix": "", "attribute": "field5", "value": "value5"},
          ]
        }
      ]
    }
  ]
}
{% endhighlight %}

### Subfiltering
You can set up subfilters to combine conditions across multiple forms that are linked together.  You can specify a subfilter by providing the `filter` key on a condition instead of `value`.  The `filter` key may only be used on attributes that represent a linked form.  The value of the `filter` key is an entire filter object, with the exact same format and rules as the main filter object.  However, the conditions on the subfilter apply to the linked form rather than directly to the primary form, so fields from the linked form may be specified as attributes.

Subfilters may be nested up to 5 levels deep.

Subfilters can be set up on either belongsTo or hasOne linked form relations.

#### belongsTo links
The most common subfilter type is belongsTo.  For example, say we have two forms with the following fields: 

* __Companies__ (form1)
	* field10: "Company Name"
	* field11: "Industry"
* __Employees__ (form2)
	* field20: "Employee Name"
	* field21: "Department"
	* field22: "Linked Company" (linked field pointing to form1)

Note field22, which is a linked form pointing form2 to form1.  This establishes a relation where Employee belongsTo Company, and Company hasMany Employees - Company is the parent form and Employee is the child form.

Let's say we need a list of accounting employees with experience in the energy industry.  We can set up a filter on Employees with a subfilter on Companies as follows:
{% highlight js%}
{
  "and": [
    {"prefix": "", "attribute": "field21", "value": "accounting"},
    {"prefix": "", "attribute": "field22", "filter": {
      "and": [
        {"prefix": "", "attribute": "field11", "value": "energy"}
      ]
    }}
  ]
}
{% endhighlight %}

Note that the subfilter uses the `attribute` "field22", the linked field.  In this subfilter we are using field11, a field from the Companies form.

This subfilter works by fetching the set of Companies matching the condition that field11 is equal to "energy", and then yielding the set of Employees whose field22 value is one of the set of resulting companies.

#### hasOne links
Typically subfilters are used to drill down a child form based on conditions on the parent form.  However, if you set a one-to-one constraint on your linked field you can also use subfilters in the other direction.  For example, say we have two forms with the following fields:

* __Users__ (form3)
	* field30: "User name"
	* field31: "Account type"
* __Profiles__ (form4)
	* field40: "Age"
	* field41: "Country"
	* field42: "Linked User" (linked field pointing to form3 - one-to-one constraint selected)

Note field42, which is a linked form pointing form4 to form3.  This establishes a relation where Profile belongsTo User, and User hasOne Profile - User is the parent form and Profile is the child form.

Let's say we need a list of premium users aged 21 or older.  We can set up a filter on Users with a subfilter on Profiles as follows:
{% highlight js%}
{
  "and": [
    {"prefix": "", "attribute": "field31", "value": "premium"},
    {"prefix": "", "attribute": "form4", "filter": {
      "and": [
        {"prefix": "min", "attribute": "field40", "value": "21"}
      ]
    }}
  ]
}
{% endhighlight %}

Note that the subfilter uses the `attribute` "form4".  Because User is the parent form, it does not have a linked field to Profile - the link is the other way around.  Because this is a one-to-one link, we are able to use the special "form{id}" `attribute` to specify a subfilter.

For information on how to use a data filter through the API, checkout [CalculationSettings]({{site.baseurl}}/rest-api/resources/#!/calculation_settings) and [DataViews]({{site.baseurl}}/rest-api/resources/#!/data_views).
