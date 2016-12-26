function slideBack(parent) {
    var select = $("<select class='dropdown' id='set_chose'>")
    if (parent.attr("id") == "post_creator") {
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
        })
        $("#set_div").html(select)
        var myvar = setTimeout(function() {
            $("#set_chose").easyDropDown()
        }, 1000)
        clearTimeout(myvar);
    } else if (parent.attr("id") == "sharebox") {
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
        })
        $("#post_set").html(select)
        var myvar = setTimeout(function() {
            $("#post_set").easyDropDown()
        }, 1000)
        clearTimeout(myvar);
    }
}

function setCreator(callerObject) {
    callerObject = callerObject[0]
    var categories = []
    var setCreator = new Box("set_creator", "75", "75")
    setCreator.createOverlay(0)
    setCreator.heading = "Create Set"
    setCreator.onclose = "slideBack"
    setCreator.onCloseArg = callerObject;
    setCreator.entry_animation = ["fly","right","250"]
    setCreator.exit_animation = ["fly","right","500"]
    var main_body = setCreator.createBox()

    var left_panel = $("<div>")
    left_panel.css({
        "float": "left",
        "width": "35%",
        "height": "100%",
        "-webkit-box-shadow": "3px 0 3px #ccc",
        "-moz-box-shadow": "3px 0 3px #ccc",
        "box-shadow": "3px 0 3px #ccc",
        "-o-box-shadow": "3px 0 3px #ccc"
    })
    var search = $("<input id='search_category' type='text' placeholder='Search Categories' class='mt1 ml1'>")
    left_panel.html(search)
    main_body.html(left_panel)
    search.css({
        "width": left_panel.width() - 30
    })
    var category_panel = $("<div id='category_panel'>")
    left_panel.append(category_panel)
    category_panel.css({
        "width": "100%",
        "height": left_panel.height() - 60,
        "margin-top": "10px",
        "overflow": "auto"
    })

    $.ajax({
        url: "manager/CategoryManager.php",
        cache: true,
        type: "get",
        dataType: "json",
        data: "req=get_categories",
        beforeSend: function() {
            category_panel.append("<center><img src='img/ajax_loader_horizontal.gif'></center>")
        },
        success: function(data) {
            category_panel.children("center").remove()
            var i
            for (i = 0; i < data.length; i++)
            {
                var category_block = $("<div style='display:table;border-bottom:1px solid #ccc;width:100%;'>")
                categories[i] = [category_block, data[i].id, data[i].name, 1]
                category_block.addClass("cp")
                category_block.css({
                    "padding-top": "5px",
                    "padding-bottom": "5px"
                })
                category_block.hover(function() {
                    $(this).css({
                        "background": "#007dff",
                        "color": "white"
                    })
                }, function() {
                    $(this).css({
                        "background": "white",
                        "color": "black"
                    })
                })
                var img = $("<img>")
                img.attr("src", data[i].img_src)
                img.css({
                    "width": "48px",
                    "height": "32px",
                    "border": "2px solid white"
                })
                img.addClass("uni_shadow_light")
                img.addClass("ml1")
                category_block.html(img)

                var category_id = $("<input type='hidden'>")
                category_id.val(data[i].id)
                category_block.append(category_id)

                var category_name = $("<span>")
                category_name.addClass("ml1")
                category_name.html(data[i].name)
                category_block.append(category_name)
                category_panel.append(category_block)

                category_block.click(function() {
                    for (i = 0; i < categories.length; i++)
                    {
                        if (categories[i][1] == $(this).children("input").val() && categories[i][3] == 1) {
                            categories[i][3] = 0
                            break
                        }
                    }
                    $(this).hide('slide', {
                        direction: 'right'
                    }, 150, function() {
                        var category_window = $("<div class='cat_win'>")
                        category_window.css({
                            "float": "left",
                            "display": "none"
                        })
                        var img = $("<img>")
                        img.attr("src", $(this).children("img").attr("src"))
                        category_window.html(img)
                        img.width("100%")
                        img.addClass("uni_shadow_light")
                        category_window.addClass("mt1")
                        category_window.css({
                            "margin-left": "30px"
                        })
                        category_window.addClass("cp")
                        img.css({
                            "border": "5px solid white",
                            "background": "white",
                            "padding-bottom": "30px"
                        })
                        category_window.hover(function() {
                            $(this).find("img").addClass("gen_hover_shadow")
                            remove.css({
                                "visibility": "visible"
                            })
                        }, function() {
                            $(this).find("img").removeClass("gen_hover_shadow")
                            remove.css({
                                "visibility": "hidden"
                            })
                        })
                        var cat_name = $("<p style='margin-top:-35px;'>")
                        cat_name.addClass("polaroid_font")
                        cat_name.html($(this).children("span").html())
                        category_window.append(cat_name)

                        var cat_id = $("<input type='hidden'>")
                        cat_id.val($(this).children("input").val())
                        category_window.append(cat_id)

                        category_window_area.prepend(category_window)
                        category_window.width(main_panel.width() / 4)
                        img.height(img.width() / 1.5)
                        category_window.show("slide", {direction: "left"}, 150, function() {
                            category_window.width(main_panel.width() / 4)
                            img.height(img.width() / 1.5)
                        })

                        var remove = $("<p>")
                        remove.html("Remove")
                        remove.css({
                            "visibility": "hidden",
                            "text-align": "center",
                            "margin-top": "10px",
                            "color": "#007dff"
                        })
                        remove.hover(function() {
                            remove.css({
                                "color": "#14b30e",
                                "text-decoration": "underline"
                            })
                        }, function() {
                            remove.css({
                                "color": "#007dff",
                                "text-decoration": "none"
                            })
                        })
                        remove.click(function() {
                            category_window.fadeOut(300)
                            for (i = 0; i < categories.length; i++)
                            {
                                if (categories[i][1] == $(this).parent().find("input").val() && categories[i][3] == 0) {
                                    categories[i][3] = 1
                                    break
                                }
                            }
                            $(this).parent().remove()
                            var e = categories[i][0]
                            e.show('slide', {
                                direction: 'right'
                            }, 150)
                        })
                        category_window.append(remove)
                    })
                })
            }
        }
    })
    search.keyup(function() {
        var item = $.trim(search.val())
        item = item.toLowerCase()
        var i
        if (item.length > 0) {
            for (i = 0; i < categories.length; i++) {
                if ((categories[i][2]).toLowerCase().indexOf(item) > -1 && categories[i][3] == 1) {
                    categories[i][0].show()
                } else {
                    categories[i][0].hide()
                }
            }
        } else {
            for (i = 0; i < categories.length; i++) {
                if (categories[i][3] == 1)
                    categories[i][0].show()
            }
        }
    })
    var main_panel = $("<div>")
    main_panel.css({
        "margin-left": "35%",
        "height": "100%",
        "overflow": "hidden",
        "width": main_body.width() - left_panel.width() - 1,
        "background": "#fafafa"
    })

    var set_detail = $("<div>")
    main_panel.append(set_detail)
    set_detail.width(main_panel.width() - 1)

    var set_name = $("<input type='text' placeholder='Enter name of this set'>")
    set_name.css({
        "float": "left",
        "margin-left": "10px",
        "width": (set_detail.width() / 1.5)
    })
    set_detail.html(set_name)

    set_detail.css({
        "position": "absolute",
        "margin-left": "1px",
        "border-left": "1px solid white",
        "padding-top": "10px",
        "padding-bottom": "10px",
        "background": "white",
        "box-shadow": "0 3px 3px #ccc",
        "-o-box-shadow": "0 3px 3px #ccc",
        "-webkit-box-shadow": "0 3px 3px #ccc",
        "-moz-box-shadow": "0 3px 3px #ccc",
        "z-index": "1090"
    })
    main_body.append(main_panel)
    var category_window_area = $("<div>")
    category_window_area.width(main_panel.width())
    category_window_area.css({
        "margin-top": "55px",
        "height": main_panel.height() - 110,
        "overflow-x": "hidden",
        "overflow-y": "auto"
    })
    main_panel.append(category_window_area)

    var base_panel = $("<div>")
    base_panel.width(main_panel.width())
    base_panel.height("50px")
    base_panel.css({
        "position": "absolute",
        "background": "white",
        "margin-top": "3px",
        "margin-left": "1px",
        "border-top": "1px solid #ccc",
        "border-bottom": "1px solid #ccc",
        "box-shadow": "0 -3px 3px #ccc",
        "-o-box-shadow": "0 -3px 3px #ccc",
        "-webkit-box-shadow": "0 -3px 3px #ccc",
        "-moz-box-shadow": "0 -3px 3px #ccc"
    })

    var create = $("<input type='button' class='bbutton fr mr2 mt1' style='width:80px'>")
    create.val("Create")
    base_panel.html(create)
    main_panel.append(base_panel)
    create.click(function() {
        var selected_interests = [], i = 0, name = $.trim(set_name.val()), err = 0
        $(".cat_win").each(function() {
            selected_interests[i] = $(this).children("input").val()
            i++
        })
        if (i == 0) {
            error_area.html("You must choose atleast one category")
            err = 1
        } else
            error_area.html("")
        if (name.length == 0) {
            set_name.addClass("errorInput")
            err = 1
        }
        else
            set_name.removeClass("errorInput")
        if (err == 0) {
            $.ajax({
                url: "manager/SetsManager.php",
                type: "get",
                cache: false,
                data: "req=create&name=" + name + "&cid=" + selected_interests + "&user_id=" + user_id,
                beforeSend: function() {
                    create.replaceWith("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin-right:30px;margin-top:20px;' id='sc_loader'>")
                },
                success: function(html) {
                    if (html == -1) {
                        error_area.html("Some error occured. Please try again later")
                        $("#sc_loader").replaceWith(create)
                    } else {
//                            $.ajax({
//                                url: "manager/UserManager.php",
//                                type: "post",
//                                data: "req=ss1over&user_id=" + user_id + "&set_id=" + html,
//                                success: function() {
//                                    location.reload()
//                                }, error: function(e) {
//                                    alertBox("Some error occured. Please try again later.")
//                                }
//                            })
                        category_list[category_list.length] = {"id": html, "name": name}
                        setCreator.closeBox()
                        slideBack(callerObject)
                    }
                }, error: function(e) {
                    error_area.html("Some error occured. Please try again later")
                    $("#sc_loader").replaceWith(create)
                }
            })
        }
    })

    var error_area = $("<span>")
    base_panel.prepend(error_area)
    error_area.css({
        "color": "red"
    })
    error_area.addClass("ml1")
}