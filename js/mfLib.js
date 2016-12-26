function mfLib(args){
    var id1 = args[0]
    var id2 = args[1]
    var mfLib = new Box("mfLib", 35, 80)   
    mfLib.heading="Mutual friends"
    mfLib.createOverlay(1)
    var main_body = mfLib.createBox()
    $.ajax({
        url:"manager/FriendManager.php",
        cache:true,
        dataType:"json",
        type:"get",
        data:"req=get_mutual_friends&id1="+id1+"&id2="+id2,
        beforeSend:function(){
            main_body.html("<center><img src='img/massive_ajax_loader.gif' style='width:40px;margin-top:50px;'></center>")
        },
        success:function(data){
            main_body.html("")
            if(data.length==0 || data[0]==-1){
                main_body.html("<div style='text-align:center'>No mutual friends</div>")
                return
            }
            var i
            for(i=0;i<data.length;i++){
                var frnd_block = $("<div>")
                var img = $("<img class='rounded' style='width:40px;height:40px;'>")
                img.attr("src",data[i].profile_pic)
                frnd_block.html(img)
                frnd_block.append("<a href='profile.php?id="+data[i].id+"' class='ml1' style='vertical-align:top'>"+data[i].name+"</a>")
                frnd_block.addClass("med_list_hoverless")
                main_body.append(frnd_block)
            }
        },
        error:function(){
            mfLib.closeBox()
            alertBox("Some problem occured. Please try again later.")
        }
    })
}