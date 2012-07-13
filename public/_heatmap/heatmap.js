$(document).ready(function(){
  tableCellSize = 60;
  first = parseFloat($('td[contenteditable="true"]:first').text());
  range = {'max':first, 'min':first};
  flags = 
  {
    'wasExtreme'     : false,
    'showingNumbers' : true,
  };
  rows = 2;
  cols = 2;
  $('table').after('<a id="plusrow">+</a><a id="pluscol">+</a>');
  $('#heatmap').css({
                      'width'  : $('#heatmap').width()  + $('#pluscol').width() ,
                      'height' : $('#heatmap').height() + $('#plusrow').height()
                    });
  $('td[contenteditable="true"]').selectOnFocus();
  $('#tabBar a').click(function(event)
  {
    event.preventDefault();
    $('#tabbedViews div').hide();
    $($(this).attr('href')).show();
  });
  findRange();
  paintCells();
  $('#tabBar a:last').click();
// size buttons
  $('#increaseCellSize').click(function(event)
  {
    event.preventDefault();
    tableCellSize++;
    tableCellSizeChanged();
  });
  $('#decreaseCellSize').click(function(event)
  {
    event.preventDefault();
    tableCellSize--;
    tableCellSizeChanged();
  });
  $('#cellSize').focus(function()
  {
    SelectText($(this).get(0));
  }).mouseup(function(event)
  {
    event.preventDefault();
  }).blur(function()
  {
    tableCellSize = parseInt($(this).text());
    tableCellSizeChanged();
  });
//handle raw text***************************************************
  $('form').submit(function(event)
  {
    event.preventDefault();
    var lines = $('textarea:first').val().split('\n');
    $.each(lines, function(i,v)
    {
      lines[i] = v.trim().split(/[^0-9.-]+/);
    });
    $('#heatmap > table > thead > tr').html('<th>index</th><th>1</th>');
    $('#heatmap > table > tbody').html('<tr><td class="label">1</td><td contenteditable="true">0</td>');
    $('#heatmap > table').css({'height':'121px','width':'121px'});
    $('#heatmap > table > tbody > tr > td[contenteditable="true"]').selectOnFocus();
    $('#heatmap').css(
    {
      'height':121 + $('#plusrow').height(),
      'width': 121 + $('#pluscol').width()
    });
    rows = 1;
    cols = 1;
    addCols(lines[0].length - 1);
    addRows(lines.length - 1);
    //echo(lines.length +' '+ lines[0].length);
    $('#heatmap > table > tbody > tr').each(function(row)
    {
      $(this).children('td[contenteditable="true"]').each(function(column)
      {
        //echo(row + ' ' + column);
        //echo(lines[row][column]);
        $(this).text(lines[row][column]);
      });
    });
    findRange();
    paintCells();
    return false;
  });
//add a row*********************************************************
  $('#plusrow').click(function()
  {
    addRows(1);
  });
//add a column*********************************************************
  $('#pluscol').click(function(){
    addCols(1);
  });
//show & hide numbers****************************************************
  $('#toggleNumbers').click(function(event)
  {
    event.preventDefault();
    flags.showingNumbers = !flags.showingNumbers;
    if(!flags.showingNumbers)
    {
      $('td[contenteditable="true"]').css('color',function(index,value)
      {
        return $(this).css('background-color');
      });
    }
    else
    {
      $('td[contenteditable="true"]').css('color','black');
    }
  });
});