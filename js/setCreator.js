function slideBack(parent) {
    if (parent.attr("id") == "post_creator") {
        var select = $("<select class='dropdown' id='set_chose'>")
        parent.show("slide", {direction: "left"}, 500, function() {
            $("#set_chose").remove()
            var i, option = []
            var cho = $("<option value='-1'>")
            cho.html("Choose a set")
            select.html(cho)
            for (i = 0; i < user_sets.length; i++) {
                option[i] = $("<option value='" + user_sets[i].id + "'>")
                option[i].html(user_sets[i].name)
                select.append(option[i])
            }
            $("#set_div").html(select)
            var myvar = setTimeout(function() {
                $("#set_chose").easyDropDown()
            }, 1000)
            clearTimeout(myvar);
        })
    } else if (parent.attr("id") == "sharebox") {
        var select = $("<select class='dropdown' id='post_set'>")
        parent.show("slide", {direction: "left"}, 500, function() {
            $("#post_set").remove()
            var i, option = []
            var cho = $("<option value='-1'>")
            cho.html("Choose a set")
            select.html(cho)
            for (i = 0; i < user_sets.length; i++) {
                option[i] = $("<option value='" + user_sets[i].id + "'>")
                option[i].html(user_sets[i].name)
                select.append(option[i])
            }
            $("#sinp").html(select)
            var myvar = setTimeout(function() {
                $("#post_set").easyDropDown()
            }, 1000);
            clearTimeout(myvar);
        });
    }
}

var added_categories = []
function setCreator(callerObject) {
    callerObject = callerObject[0]
    var setCreator = new Box("set_creator", "85", "75")
    setCreator.createOverlay(0)
    setCreator.heading = "Create Set"
    setCreator.onclose = "slideBack"
    setCreator.onCloseArg = callerObject;
    setCreator.entry_animation = ["fly", "right", "250"]
    setCreator.exit_animation = ["fly", "right", "500"]
    var main_body = setCreator.createBox()

    var desc_exists = 0;
    var top_setting = $("<div style='width:100%;padding:20px;padding-top:10px;width:calc(100% - 2.5em);'>");
    main_body.html(top_setting);
    var set_name = $("<input id='set_name' type='text' placeholder='Set name' class='seemless_input' style='width:80%;height:50px;'>");
    top_setting.html(set_name);
    top_setting.append("<br><br>")
    var desc_label = $("<a href='#'>")
    desc_label.html("Add Description");
    top_setting.append(desc_label);
    var create = $("<input type='button' value='Create' class='gbutton fr' style='width:80px;margin-right:20%;margin-right:calc(20% - .625em);'>")
    top_setting.append(create);
    top_setting.append("<br>");
    desc_label.click(function() {
        if (desc_exists == 0) {
            $("#set_desc").show("slide", {direction: "up"}, "500");
            $(this).html("Remove Description");
            desc_exists = 1;
        } else {
            $("#set_desc").hide("slide", {direction: "up"}, "500");
            $(this).html("Add Description");
            desc_exists = 0;
        }
    })
    var set_desc = $("<textarea placeholder='Description of your set' class='mt1' id='set_desc' style='padding:5px;resize:none;width:50%;height:10em;display:none;'></textarea>")
    top_setting.append(set_desc)

    var category_panel = $("<div id='category_panel' style='width:100%;padding:20px;padding-top:0;width:calc(100% - 2em);background:#fff;'>");
    main_body.append(category_panel);

    if (categories == null) {
        $.ajax({
            url: "manager/CategoryManager.php",
            cache: true,
            type: "get",
            dataType: "json",
            data: "req=get_categories",
            beforeSend: function() {
                category_panel.append("<center><img src='img/ajax_loader_horizontal.gif'></center>")
            }, success: function(data) {
                categories = data;
                category_panel.html("");
                displayCategoryList(categories, category_panel);
            }, error: function(e, f) {
                alertBox()
            }
        })
    } else {
        displayCategoryList(categories, category_panel);
    }
    create.click(function() {
        var name = $.trim($("#set_name").val()), desc = "", button = $(this);
        if ($("#set_desc").length > 0)
            desc = $.trim($("#set_desc").val());
        $.ajax({
            url: "manager/SetsManager.php",
            type: "get",
            cache: false,
            data: "req=create&name=" + encodeURIComponent(name) + "&desc=" + encodeURIComponent(desc) + "&cid=" + added_categories + "&user_id=" + user_id,
            beforeSend: function() {
                button.replaceWith("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin-right:20%;margin-right:calc(20% - .625em);' id='sc_loader'>")
            }, success: function(id) {
                if (id == -1) {
                    $("#sc_loader").replaceWith(button)
                } else {
                    added_categories.splice(0, added_categories.length);
                    user_sets[user_sets.length] = {"id": id, "name": name}
                    setCreator.closeBox();
                    slideBack(callerObject);
                }
            }, error: function(e) {
                $("#sc_loader").replaceWith(create)
            }
        })

    })
}

function displayCategoryList(categories, category_panel) {
    var i;
    for (i = 0; i < categories.length; i++) {
        var category = $("<div id='cat" + categories[i].id + "' style=\"float:left;width:160px;height:90px;border:5px solid white;border-bottom:30px solid white;position:relative;margin-right:10px;margin-bottom:10px;cursor:pointer;\" class='uni_shadow_light cat'>")
        var cat_show = $("<div class='cat_show' style=\"background-image:url('" + categories[i].image_src + "');background-size:cover;background-repeat:no-repeat;width:100%;height:100%;\">")
        category.html(cat_show);
        var checked_show = $("<div class='checked_show' style='display:none;'>");
        var center = $("<center>");
        checked_show.html(center);
        var tick = $("<img src='img/tick.png' style='width:30px;margin-top:30px;'>")
        center.html(tick);
        center.append("<p class='polaroid_font'>" + categories[i].name + "</p>");
        category.append(checked_show);
        var p = $("<p style='position:absolute;text-align: center;width:100%;font-size:15px;' class='polaroid_font'>");
        p.html(categories[i].name);
        category.append(p);
        category_panel.append(category);
        category.click(function() {
            var id = $(this).attr("id").substring(3), index;
            var cshow = $(this).children(".cat_show");
            var ptext = $(this).children(".polaroid_font");
            var chshow = $(this).children(".checked_show");
            if ((index = $.inArray(id, added_categories)) == -1) {
                added_categories[added_categories.length] = id;
                cshow.hide("slide", {direction: "left"}, "150");
                ptext.hide("slide", {direction: "left"}, "150", function() {
                    chshow.show("slide", {direction: "right"}, "75");
                });
            } else {
                added_categories.splice(index, 1);
                chshow.hide("slide", {direction: "right"}, "75", function() {
                    cshow.show("slide", {direction: "left"}, "150");
                    ptext.show("slide", {direction: "left"}, "150");
                });
            }
        })
    }
    var h1 = $("<h3 style='font-weight:lighter;margin-bottom:10px;margin-top:-10px;'>");
    h1.html("Choose categories");
    category_panel.prepend(h1);
}