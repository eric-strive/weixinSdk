
    function control(rfunc,lfunc,rbgcolor,lbgcolor){
        var insideWidth=$(".inside").width();
        var outsideWidth=$(".outside").width();
        if(rbgcolor == undefined || rbgcolor==''){
        	var rbgcolor='#fd4900';
        }else{
        	var rbgcolor=rbgcolor;
        }


        if(lbgcolor == undefined || lbgcolor==''){
        	var lbgcolor='#ccc';
        }else{
        	var lbgcolor=lbgcolor;
        }
 

        $(".inside").click(function (){
            if($(".inside").position().left==0){
                $(this).stop().animate({
                    left:outsideWidth-insideWidth,
                },500);
                setTimeout(function (){
                    $(".outside").css('background',rbgcolor);
                },400);
                if(rfunc !== undefined) rfunc();
            }
            if($(".inside").position().left==outsideWidth-insideWidth){
                $(this).stop().animate({
                    left:0,
                },500);
                setTimeout(function (){
                    $(".outside").css('background',lbgcolor);
                },400); 
				if(lfunc !== undefined) lfunc();	
            } 
        })
    }

