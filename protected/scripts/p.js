$(document).ready(function()
{
  document.body.className = 'alive';

  if(document.body.id === 'home')
  {

  }
  else if(document.body.id === 'code')
  {
    $('#code #content .toc a').click(function(evt)
    {
      evt.preventDefault();
      var state = $(this).attr('href').substring(1);
      document.location.hash = '!/' + state + '/';
      document.getElementById('content').className = state;
      if(!window.hasOwnProperty('orientation'))//don't scale height on mobile
      {
        $('#projects').css('height',$('#' + state).css('height'));
      }
    });  
    var hashRequest = document.location.hash === '' ? '#toc' : '#' + document.location.hash.split('/')[1];
    $('#code #content .toc a[href="' + hashRequest + '"]').click();
    if(window.hasOwnProperty('orientation'))//fake scrollbars on mobile
    {
      $('pre').append('<div class="thumb"></div>').each(function()
      {
        var thumb = $(this).children('div.thumb');
        var width = $(this).width();
        var lp = $('pre').css('padding-left');
        lp = parseInt(lp.substr(0,lp.length-2),10);
        width = width * width;
        width = width / this.scrollWidth;
        thumb.width(width);
        $(this).data('thumbScrollRange',($(this).width()-width));
        $(this).data('paneScrollRange', (this.scrollWidth+lp-$(this).width()));
        console.log(this.scrollWidth+' - '+$(this).width()+' = '+$(this).data('paneScrollRange'));
      });
      $('pre').scroll(function()
      {
        var thumb = $(this).children('div.thumb');
        var paneScroll = this.scrollLeft/$(this).data('paneScrollRange');
        paneScroll = paneScroll > 1 ? 1 : paneScroll;
        var thumbScroll = paneScroll * $(this).data('thumbScrollRange');
        thumb.css('left',(this.scrollLeft + thumbScroll)+'px');
        console.log(this.scrollLeft+'\t'+$(this).data('paneScrollRange')+'\t'+paneScroll+'\t'+thumbScroll);
      });
    }
    //$('pre').tinyscrollbar();
  }
  else
  {
    console.log('unknown page.');
  }
});