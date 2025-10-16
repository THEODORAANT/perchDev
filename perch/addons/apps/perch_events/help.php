<p>The Events app enables you to list upcoming events, organise them into categories, and publish calendars or listings on your site.</p>

<h2>Listing event categories</h2>
<p>Use <code>perch_events_categories()</code> to output links to the categories you have created. The function accepts an optional array of settings so you can control which categories are returned.</p>

<ul>
    <li><code>template</code> &ndash; set an alternative template for each category.</li>
    <li><code>include-empty</code> &ndash; pass <code>true</code> to show categories even when they have no upcoming events.</li>
    <li><code>past-events</code> &ndash; pass <code>true</code> to base the counts on past events instead of future ones.</li>
    <li><code>exclude</code> &ndash; supply a slug, title, ID, or an array of any combination of these values to omit specific categories from the output.</li>
</ul>

<p>Example:</p>
<pre><code>&lt;?php perch_events_categories([
    'exclude' => ['Promo', 'internal-updates']
]); ?&gt;</code></pre>

<p>In the example above, both the category titled “Promo” and the category whose slug is <code>internal-updates</code> will be removed from the rendered list.</p>
