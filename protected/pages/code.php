<div class="toc">
  <ul>
<?php
$lines = file(__FILE__);
$ids = preg_grep('/^[ \t]+<div.*class="project"/', $lines);
$projects = preg_grep("/^[ \t]+<h2>/", $lines);
$ids = array_values($ids);            //normalize the arrays to be 0 based
$projects = array_values($projects);
$patterns = array("/<h2/","/\/h2/");
foreach($projects as $key => $value)
{
  $id = trim(preg_replace(array("/<.*id=\"/","/\">/"),array('',''),$ids[$key]));
  $replacements = array('  <li><a href="'."#$id".'" class="toc-projects"',"/a></li"); //replace with list items holding links to hashed IDs
  echo(preg_replace($patterns,$replacements,$value));
}
?>
  </ul>
  </div>
  <div id="projects">
    <div class="project" id="toc">
      <h2>Easy Table of Contents</h2>
      <h3>Table of Contents in the Code Page</h3>
      <p>
        To make the table of contents on this page,
        I really wanted to do everything server side.
        That way, I would offer a consistent experience for users with and without
        Javascript.
        But I didn't want to have to maintain an actual table of contents
        section of this page by hand, as I'm just not into that.
        The solution then was to use a snippet of PHP to scan this page for sections and 
        generate the ToC for me.
      </p>
      <p>
        Each project on this page is in a <code>div</code> of class "project."
        The <code>id</code> of the div is how we will navigate to each, either with or
        without Javascript.
        Each <code>div</code> has a child <code>h2</code> with the title of the project inside.
        We, then, want to make a list of links with targets taken from the <code>div</code>
        elements and their text taken from the <code>h2</code> elements.
        Because the page is structured logically, I didn't bother parsing out the DOM
        structure; rather, I just used grep and new that every time I find a <code>div</code>
        I'm going to find a corresponding <code>h2</code>.
        I modified these regexes to make sure that they don't detect themselves in the page.
        Using the various utilities for stripping out all PHP and HTML tags would
        have been cool too, but I wanted to avoid unnecessary processing.
      </p>
<pre><code>$lines = file(__FILE__);
$ids = preg_grep('/^[ \t]+&lt;div.*class="project"/', $lines);
$projects = preg_grep("/^[ \t]+&lt;h2>/", $lines);
$ids = array_values($ids);            //normalize the arrays to be 0 based
$projects = array_values($projects);
$patterns = array("/&lt;h2/","/\/h2/");
foreach($projects as $key => $value)
{
  $id = trim(preg_replace(array("/&lt;.*id=\"/","/\">/"),array('',''),$ids[$key]));
  $replacements = array('  &lt;li>&lt;a href="'."#$id".'"',"/a>&lt;/li"); //replace with list items holding links to hashed IDs
  echo(preg_replace($patterns,$replacements,$value));
}  </code></pre>
    <h3>Styling the Projects in CSS</h3>
    <p>
      If we want the CSS to be completely agnostic to the contents of the code page,
      we're going to have to be a little crafty in there as well.
      To begin with, I'm delivering my CSS via PHP.
      That's the only way to do this sort of thing.
      Since it&rsquo;s going through PHP, there's some extra header related stuff
      I have to do to send the last modified and expiration dates of the file.
      With serving normal CSS files, Apache handles this its own dang self.
      That&rsquo;s also in the code.
    </p>
    <p>
      The PHP/CSS file basically does a shortened version of the above scanning,
      only it instead has to make a selector and style for each project.
      The projects are all side by side in a massive <code>div</code>
      called <code>projects</code> that slides to reveal a given project.
      So each project corresponds to a <code>left</code> value for the <code>projects</code>.
      The code follows below.
    </p>
<pre><code><?php echo(htmlentities(file_get_contents('p.php')));?></code></pre>
  </div>
  <div class="project" id="whom">
    <h2>Whom to Follow</h2>
    <p>
      Whom to follow is a user script that changes the "Who to Follow"
      link on Twitter's top bar to "Whom to Follow." I wrote it because
      that thing was driving me up a wall.
    </p>
    <p>
      I've only tested it in Chrome, but it's so simple that it's almost
      not worth testing in Greasemonkey. Kidding. I'll test it. Install it
      <a href="/scripts/whom.user.js" title="Install 'Whom to Follow'
      script">here</a>.  If you'd prefer a bookmarklet (tested in
      IE8, Firefox, Safari, and Chrome) then run the following in
      the URL bar of your browser tab that's open to Twitter:
    </p>
    <pre><code>javascript:(function(){document.getElementsByClassName('wtf-module')[0].childNodes[1].childNodes[1].childNodes[1].innerHTML = 'Whom to Follow';}())</code></pre>
    <p>
      You can also get the script from
      <a href="http://userscripts.org/scripts/show/100443">its Userscripts page</a>.
    </p>
  </div>
  <div class="project" id="cal">
    <h2>3 Month Calendar</h2>
    <p>
      I like seeing a calendar for the previous, current, and next month. I also
      like seeing the current day highlighted. This (admittedly insane) one liner
      does all of those. I'll post the whole thing then break it down.
    </p>
    <pre><code>cal $(date '+"BEGIN{if(%m==1){print 12;print %Y-1}else{print %m-1;print %Y;}}"'|xargs awk);cal|sed "s/ $(date "+%d"|sed 's/^0/ /')/$(echo -e "\033[1;31m")&amp;$(echo -e "\033[0m")/";date '+"BEGIN{if(%m==12){print 1;print %Y+1}else{print %m+1;print %Y;}}"'|xargs awk|xargs cal</code></pre>
    <p>
      The first part of this is the previous month's date. That's easy
      enough, we just get the date from <code>date</code> and subtract 1.
      But we have to handle the case of January, where we get 12 for the
      month and the previous year. So our date string becomes an
      <code>awk</code> command which handles that. We pass it using
      <code>xargs</code> to turn the whole output into one string of
      arguments, rather than passing them one at a time like backticks or
      <code>$()</code>. We then pass the output of <code>awk</code> to
      <code>cal</code> which just prints the previous month. Easy peasy.
    </p>
    <p>
      The current month is fun. We first get the current day and, if it has
      a leading zero, replace that with a space. that then becomes the
      target of a regex in <code>sed</code>, which wraps the day in
      <abbr title="American National Standards Institute">ANSI</abbr> escape
      codes for, respectively, turning the text red and then clearing any
      effect. That regex is then applied to the output of the current
      month's calendar. We have to <code>echo -e</code> the color escape
      codes because just printing them is not enough, apparently.
    </p>
    <p>
      Finally we just do basically the same thing as the first command to
      print out the next month. I used <code>xargs</code> to pass to
      <code>cal</code> because I actually like that better. So do that.
    </p>
    <p>
      Addendum: I use this in my Geektool to display it on the desktop. In
      that case you have to leave off the <code>-e</code> option on each
      <code>echo</code> inside the regex, but otherwise leave it the same.
    </p>
    <p>
      Post addendum: I realize that the GNU date makes this easier than the
      BSD date&mdash;all you have to do is specify <code>-d "next month"</code>
      and you can skip the whole awk malarky. But BSD just doesn't have
      that.
    </p>
    <pre><code>cal $(date -d "last month" '+%m %y'); cal; cal $(date -d "next month" '+%m %y')</code></pre>
  </div>
  <div class="project" id="gallery">
    <h2>Not Terrible Gallery</h2>
    <p>
      I recently worked on <a href="http://jbmorton.net">a portfolio site for my buddy James</a>.
      He's a photographer who, among his other projects, went around the world on a Watson Fellowship,
      where he photographed the global shipping industry.
    </p>
    <p>
      The challenge on the front end was making it not look like every other photo website.
      I wanted to implement his raster design concepts in 
        <abbr title="Cascading Style Sheets">CSS</abbr> with decent animation in jQuery, but
      without alienating what few visitors wouldn't be using Javascript.
      To that end I did pretty OK.
      The everything but the landing page uses an invisible navigation ribbon at the top that shows
      itself when you hover it.
      The omnipresent logo at the top left hints that there is more up top in order to navigate.
      To be honest, it's not the design I would have chosen;
      I prefer users to know what their navigation options are at all times.
      But one renders unto the client that which is the client's, so I did what was asked.
      The navigation ribbon is always present if you don't have JS enabled.
      It does cover the photos somewhat&mdash;in the future I would make the default
        <abbr title="Cascading Style Sheets">CSS</abbr> push the photos down,
      then have the Javascript push them back up if it can hide the ribbon.
    </p>
    <p>
      The other thing I'm not crazy about is the way the logo shows up on interior pages.
      Without it cached, it loads last.
      This is because it is the background to the title heading, whose text is off screen.
      Semantically, this makes sense: the image is not part of the content.
      Furthermore, the title text gets scraped first, by search engines,
        so you get less of the navigation ribbon and more of the title and subtitle in the
        <a href="https://www.google.com/search?hl=en&q=%22the%20merchant%20and%20the%20leviathan%22">search engine summary.</a>
      However, background images get loaded last.
      To combat this, I would make the photos on the page the backgrounds of their list items, instead of images within them.
      However, this concern was obviated when he decided to go to a
        <abbr title="Content Management System">CMS</abbr>.
    </p>
    <p>
      On the backend, I ended up making a
        <a href="http://en.wikipedia.org/wiki/Number_8_wire">&#8470; 8 wire</a>
        filesystem based
        <abbr title="Content Management System">CMS</abbr>.
      The gist is that it scrapes directories corresponding to galleries for the photos in that gallery.
      Photos are named sequentially in order to be listed in order.
      To the user it appears that galleries are folders,
        but that's actually a URL rewrite to make it clean for linking and scraping.
      The end result was that the client&mdash;James&mdash;only had to edit one file and upload photos
      in order to make a new gallery.
    </p>
    <p>
      I decided not to use the Flickr
      <abbr title="Application Programming Interface">API</abbr>
      to make his page because I thought their licensing terms would have made it a bit tacky.
      Specifically, all photos must reside in links to the original photo's page on Flickr.
      I figured that my "FTP and one file edit" design was sufficient.
    </p>
    <p>
      Of course, a horse lead to water does not always drink; he's moving over to a new CMS with a new developer.
      I should probably mirror the work I did.
    </p>
  </div>
  <div class="project" id="heatmap">
    <h2>Heatmap</h2>
    <p>
      I like heatmaps. They're a pretty cool way of expressing 3D data in two dimensions.
      I've been working on a web tool to make quick little ones.
      <a href="/_heatmap">Check it out.</a>
      I've got a ton of features I want to add in the coming days and weeks and such, but I couldn't
      not give you a taste.
      Right now the only way to export the data is to hide the numbers and take a screenshot.
      It's what I've got right now.
      And obviously if you don't have Javascript please stay home.
    </p>
  </div>
</div><!--projects-->