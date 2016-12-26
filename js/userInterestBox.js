function userInterestBox(user_interests,image_src){
    var list_interests = new Box("list_interests",70,80),i,interests = []
    list_interests.heading = "My Interests"
    list_interests.entry_animation = ["fly","top",150]
    list_interests.exit_animation = ["fly","top",150]
    list_interests.createOverlay()
    var main_body = list_interests.createBox()
    var interest_search = $("<input>")
    interest_search.attr({
        "type":"text",
        "placeholder":"Search interests"
    })
    interest_search.css({
        "margin-left":"20px",
        "margin-top":"10px"
    })
    main_body.append(interest_search)
    interest_search.keyup(function(){
        var search = $(this).val().trim().toLowerCase()
        if(search.length==0){
            for(i=0;i<interests.length;i++){
                interests[i].show()
            }
        }else{
            for(i=0;i<interests.length;i++){
                var p = user_interests[i][1].toLowerCase()
                if(p.indexOf(search)==-1){
                    interests[i].hide()
                }else{
                    interests[i].show()
                }
            }
        }
    })
    var in_div = $("<div>")
    in_div.width(main_body.width() - 10)
    in_div.css({
        "margin-left":"10px"
    })
    main_body.append(in_div)
    for(i=0;i<user_interests.length;i++){
        interests[i] = $("<div>")
        interests[i].addClass("interest")
        interests[i].append($("<input>").attr({
            "type":"hidden",
            "value":user_interests[i][0]
        }))
        interests[i].append(image_src[i])
        interests[i].append("<center><p>"+user_interests[i][1]+"</p></center>")
        in_div.append(interests[i])
    }
        
    $(".interest").live("click",function(){
        $("#interest_name").html($(this).find("p").html().trim())
    })
}