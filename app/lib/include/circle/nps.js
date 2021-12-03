$('.btn').hover(function(){
    var $this = $(this);
    var $prevAll = $(this).prevAll();
    
  
    
    $this.addClass('btn-warning');
    $prevAll.addClass('btn-warning');
 }, function() {
    var $this = $(this);
    var $prevAll = $(this).prevAll();
    
    $this.removeClass('btn-warning');
    $prevAll.removeClass('btn-warning');
 });