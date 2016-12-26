function setsLib(args){
    var user_id = args[0]
    var user_name = args[1]
    var setsLib = new Box("setsLib", 35, 80)   
    setsLib.heading=user_name+"'s sets"
    setsLib.createOverlay(1)
    var main_body = setsLib.createBox()
    $.ajax({
        url:"manager/SetsManager.php",
        cache:true,
        dataType:"json",
        type:"get",
        data:"req=get_sets&get_preview=1&user_id="+user_id,
        beforeSend:function(){
            main_body.html("<center><img src='img/massive_ajax_loader.gif' style='width:40px;margin-top:50px;'></center>")
        },
        success:function(data){
            main_body.html("")
            if(data.length==0){
                main_body.html("<div style='text-align:center'>This user doesn't have any sets to share with you.</div>")
            }
            var i
            for(i=0;i<data.length;i++){
                var set_block = $("<div>")
                var set_block_id = $("<input type='hidden' value='"+data[i].id+"'>")
                set_block.html(data[i].name)
                set_block.addClass("med_list")
                main_body.append(set_block)
                
                var view = $("<input type='button' class='wbutton fr' value='View' style='width:80px'>")
                set_block.append(view)
            }
        },error:function(){
            setsLib.closeBox()
            alertBox("Some problem occured. Please try again later.")
        }
    })
    
}
