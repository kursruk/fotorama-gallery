window.addEventListener("load", function(){     
    let $ = jQuery;
    
    if ($('.w-is-edit').length==0)
    {
        $('.thumbnail').click(function(e) {            
            let im = $( $(e.target).parents('.thumbnail:first').find('img') );            
            let img =  window.location.origin+'/'+im.attr('data-src'); 
            // console.log(img);           
            if (undefined!==window.FOTOMOTO_ERROR)  alert('Fotomoto error: '+FOTOMOTO_ERROR.msg);
            else FOTOMOTO.API.showWindow(FOTOMOTO.API.PRINT, img);
        })

    } else
    {
        $('.img-tumbnail div.w-title').blur(function(e){
            let d = $(e.target);
            d.prop('is_updated', true );            
        })
    }

    $('.b-save').click(function(){
        let items = $('.img-tumbnail div.w-title');
        let data = [];
        for (let i=0; i<items.length; i++)
        {  let d = $(items[i]);
            if ( d.prop('is_updated') ) data.push({id:d.attr('data-id'), text:d.text()} )           
        }
        if (data.length>0) $.post('?task=gallery.saveTitles',{rows:data}, rd=>{
            if (rd.updated) {
                for (let i=0; i<items.length; i++)
                {  let d = $(items[i]);
                   let id = 1*d.attr('data-id')
                   if (rd.updated[id]!==undefined) d.prop('is_updated', false)                   
                }
            }
        }, 'json' )
    })
    
});
