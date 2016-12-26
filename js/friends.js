function manageGroupPage(e) {
    var mg_div = $("<div id='mg' style='width:100%;'>")
    mg_div.height($("body").height() - 125)
    e.html(mg_div)

    var control_panel = $("<div id='control_panel' class='fl'>")
    mg_div.html(control_panel)
    var top_tab = $("<ul class='linear_list' id='top_tab'>")
    top_tab.width(control_panel.width())
    var create_group_tab = $("<li id='cgt' class='ctb'>")
    create_group_tab.html("Create Group")
    var manage_group_tab = $("<li id='mgt'>")
    manage_group_tab.html("Manage Group")
    top_tab.html(create_group_tab)
    top_tab.append(manage_group_tab)
    control_panel.html(top_tab)
    var form_div = $("<div style='width:100%;height:90%;margin-top:10px;'>")
    control_panel.append(form_div)
    var friend_panel = $("<div id='friend_panel' class='fl'>")
    mg_div.append(friend_panel)
    var search_friend = $("<input type='text' placeholder='Search' style='height:25px;'>")
    friend_panel.html(search_friend)
    friend_panel.append("<br>")
    control_panel.css("min-width", 0.268 * screen.width)
    friend_panel.css("margin-left", 0.245 * screen.width)
    createGroupUI(form_div, friend_panel)
    top_tab.children("li").click(function() {
        if ($(this).hasClass("ctb")) {
        }
        else {
            $(".ctb").removeClass("ctb")
            $(this).addClass("ctb")
            var id = $(this).attr("id")
            if (id == "cgt")
                createGroupUI(form_div, friend_panel)
            else if (id == "mgt")
                manageGroupUI(form_div, friend_panel)
        }
    })
}

function friendPanelMaker(friend_panel, purpose, friends) {
    var i
    var list_div = $("<div style='width:100%;height:100%;margin-top:10px;display:table;'>")
    friend_panel.html(list_div)
    if (purpose == "manage")
        gf_list = []

    for (i = 0; i < friends.length; i++) {
        var square
        if (purpose == "create")
            square = $("<div class='square uni_shadow_lightest fl ml1 mt1' id='sq_" + friends[i].uid + "'>")
        else
            square = $("<div class='square uni_shadow_lightest fl ml1 mt1' id='sq_" + friends[i].id + "'>")
        var user_pic = $("<img src='" + friends[i].profile_pic + "'>")
        var user_name = $("<div class='square_user_name'>")
        user_name.html("<span style='margin-left:5px;'>" + friends[i].name + "</span>")
        square.html(user_pic)
        square.append(user_name)
        list_div.append(square)
        square.width((friend_panel.width() - 70) / 6)
        if (purpose == "manage") {
            var fade = $("<div id='fade' style='position:absolute;background:black;opacity:0.9;'>")
            square.prepend(fade)
            fade.html("<center><img src='img/tick.png' style='width:50%;height:50%;magrin-left:auto;margin-top:25%;'></center>")
            fade.width(fade.parent().width())
            fade.height(fade.parent().height() - 30)
            gf_list[i] = "" + friends[i].id
        }

        square.click(function() {
            var c = $(this), index
            if (purpose == "create") {
                index = cf_list.indexOf(c.attr("id").substr(3))
                if (index == -1) {
                    cf_list.push(c.attr("id").substr(3))
                    var fade = $("<div id='fade' style='position:absolute;background:black;opacity:0.9;'>")
                    c.prepend(fade)
                    fade.html("<center><img src='img/tick.png' style='width:50%;height:50%;magrin-left:auto;margin-top:25%;'></center>")
                    fade.width(fade.parent().width())
                    fade.height(fade.parent().height() - 30)
                } else {
                    cf_list.splice(index, 1)
                    c.children("#fade").remove()
                }
            } else if (purpose == "manage") {
                index = gf_list.indexOf(c.attr("id").substr(3))
                if (index == -1) {
                    gf_list.push(c.attr("id").substr(3))
                    var fade = $("<div id='fade' style='position:absolute;background:black;opacity:0.9;'>")
                    c.prepend(fade)
                    fade.html("<center><img src='img/tick.png' style='width:50%;height:50%;magrin-left:auto;margin-top:25%;'></center>")
                    fade.width(fade.parent().width())
                    fade.height(fade.parent().height() - 30)
                } else {
                    gf_list.splice(index, 1)
                    c.children("#fade").remove()
                }
            }
        })
    }
}

function createGroupUI(form_div, friend_panel) {
    var create = createForm(form_div, null, null)
    friendPanelMaker(friend_panel, "create", friends)
    create.on("click", null, function() {
        var group_name = $.trim($("#group_name").val())
        var group_type = $("input[name=group_type]:checked", "#group_create_form").val()
        var is_block = false, is_private_sharing = false, is_suggest = false
        if ($("#block").is(":checked"))
            is_block = true
        if ($("#sharing").is(":checked"))
            is_private_sharing = true
        if ($("#suggest").is(":checked"))
            is_suggest = true
        if (group_name.length == 0) {
            $("#group_name").addClass("errorInput")
            return
        }
        if (group_type == undefined) {
            alertBox("Please chose a Group Type")
            return
        }
        
        $.ajax({
            url: "manager/FriendManager.php",
            type: "get",
            data: "req=create_group&user_id=" + user_id + "&group_name=" + group_name + "&group_type=" + group_type + "&is_block=" + is_block + "&is_private_sharing=" + is_private_sharing + "&is_suggest=" + is_suggest + "&list=" + cf_list,
            beforeSend: function(data) {
                $("#create").replaceWith("<img src='img/ajax_loader_horizontal.gif' id='create'>")
            }, success: function(data) {
                $("#create").replaceWith("<input type='button' id='create' class='bbutton' value='Create' style='width:80px;'>")
                var cmsg = $("<div style='width:80%;margin:10px;margin-left:0;padding:10px;border:1px solid #14b30e;background:#E0F8E6;color:black;text-align:center'>")
                cmsg.html("Group created successfully")
                form.append(cmsg)
                setTimeout(function() {
                    cmsg.fadeOut("1500")
                }, "3000")
                if (group_list.length != 0)
                    group_list[group_list.length] = [data, group_name]
                cf_list = []
            }, error: function(e, f) {
                alertBox()
            }
        })
    })
}

var gf_list = []
function manageGroupUI(form_div, friend_panel) {
    friend_panel.html("")
    if (group_list.length == 0) {
        $.ajax({
            url: "manager/FriendManager.php",
            type: "get",
            dataType: "json",
            data: "req=get_user_groups&user_id=" + user_id,
            beforeSend: function() {
                form_div.html("<center><img src='img/ajax_loader_horizontal.gif'></center>")
            }, success: function(data) {
                if(data.length==0){
                    form_div.html("<center>You have not created any groups</center>")
                    return
                }
                var i
                form_div.html("")
                for (i = 0; i < data.length; i++) {
                    group_list[group_list.length] = [data[i].id, data[i].name]
                    var group_option = $("<div class='group_option'>")
                    var group_name_div = $("<div class='group_name_div'>")
                    group_name_div.html(data[i].name)
                    var group_id = $("<input type='hidden' value='" + data[i].id + "'>")
                    group_option.html(group_name_div)
                    group_option.append(group_id)
                    form_div.append(group_option)
                }
                $(".group_option").click(function() {
                    var group_id = $(this).find("input").val()
                    var clist = form_div.html()
                    $.ajax({
                        url: "manager/FriendManager.php",
                        type: "get",
                        dataType: "json",
                        data: "req=get_group_details&id=" + group_id,
                        beforeSend: function() {
                            form_div.html("<center><img src='img/ajax_loader_horizontal.gif'></center>")
                        }, success: function(data) {
                            var save = createForm(form_div, group_id, data)
                            friendPanelMaker(friend_panel, "manage", data.group_friends)
                            save.on("click", null, function() {
                                var group_name = $.trim($("#group_name").val())
                                var group_type = $("input[name=group_type]:checked", "#group_manage_form").val()
                                var is_block = false, is_private_sharing = false, is_suggest = false
                                if ($("#block").is(":checked"))
                                    is_block = true
                                if ($("#sharing").is(":checked"))
                                    is_private_sharing = true
                                if ($("#suggest").is(":checked"))
                                    is_suggest = true
                                if (group_name.length == 0) {
                                    $("#group_name").addClass("errorInput")
                                    return
                                }
                                if (group_type == undefined) {
                                    alertBox("Please chose a Group Type")
                                    return
                                }

                                $.ajax({
                                    url: "manager/FriendManager.php",
                                    type: "get",
                                    data: "req=update_group&group_id=" + group_id + "&user_id=" + user_id + "&group_name=" + group_name + "&group_type=" + group_type + "&is_block=" + is_block + "&is_private_sharing=" + is_private_sharing + "&is_suggest=" + is_suggest + "&list=" + gf_list,
                                    beforeSend: function() {
                                        $("#save").replaceWith("<img src='img/ajax_loader_horizontal.gif' id='save'>")
                                    }, success: function(data) {
                                        $("#save").replaceWith("<input type='button' id='save' class='bbutton' value='Save' style='width:80px;'>")
                                        var cmsg = $("<div style='width:80%;margin:10px;margin-left:0;padding:10px;border:1px solid #14b30e;background:#E0F8E6;color:black;text-align:center'>")
                                        cmsg.html("Changes saved successfully")
                                        form.append(cmsg)
                                        setTimeout(function() {
                                            cmsg.fadeOut("1500")
                                        }, "3000")
                                    }, error: function(data) {
                                            alertBox()
                                    }
                                })
                            })
                        }, error: function(e, f) {
                            alertBox()
                            form_div.html(clist)
                        }
                    })
                })
            }, error: function(e, f) {
                alertBox()
            }
        })
    } else {
        var i
        form_div.html("")
        for (i = 0; i < group_list.length; i++) {
            var group_option = $("<div class='group_option'>")
            var group_name_div = $("<div class='group_name_div'>")
            group_name_div.html(group_list[i][1])
            var group_id = $("<input type='hidden' value='" + group_list[i][0] + "'>")
            group_option.html(group_name_div)
            group_option.append(group_id)
            form_div.append(group_option)
        }
        $(".group_option").click(function() {
            var group_id = $(this).find("input").val()
            var clist = form_div.html()
            $.ajax({
                url: "manager/FriendManager.php",
                type: "get",
                dataType: "json",
                data: "req=get_group_details&id=" + group_id,
                beforeSend: function() {
                    form_div.html("<center><img src='img/ajax_loader_horizontal.gif'></center>")
                }, success: function(data) {
                    var save = createForm(form_div, group_id, data)
                    friendPanelMaker(friend_panel, "manage", data.group_friends)
                    save.on("click", null, function() {
                        var group_name = $.trim($("#group_name").val())
                        var group_type = $("input[name=group_type]:checked", "#group_manage_form").val()
                        var is_block = false, is_private_sharing = false, is_suggest = false
                        if ($("#block").is(":checked"))
                            is_block = true
                        if ($("#sharing").is(":checked"))
                            is_private_sharing = true
                        if ($("#suggest").is(":checked"))
                            is_suggest = true
                        if (group_name.length == 0) {
                            $("#group_name").addClass("errorInput")
                            return
                        }
                        if (group_type == undefined) {
                            alertBox("Please chose a Group Type")
                            return
                        }
                        $.ajax({
                            url: "manager/FriendManager.php",
                            type: "get",
                            data: "req=update_group&group_id=" + group_id + "&user_id=" + user_id + "&group_name=" + group_name + "&group_type=" + group_type + "&is_block=" + is_block + "&is_private_sharing=" + is_private_sharing + "&is_suggest=" + is_suggest + "&list=" + gf_list,
                            beforeSend: function() {
                                $("#save").replaceWith("<img src='img/ajax_loader_horizontal.gif' id='save'>")
                            }, success: function(data) {
                                $("#save").replaceWith("<input type='button' id='save' class='bbutton' value='Save' style='width:80px;'>")
                                var cmsg = $("<div style='width:80%;margin:10px;margin-left:0;padding:10px;border:1px solid #14b30e;background:#E0F8E6;color:black;text-align:center'>")
                                cmsg.html("Changes saved successfully")
                                form.append(cmsg)
                                setTimeout(function() {
                                    cmsg.fadeOut("1500")
                                }, "3000")
                            }, error: function(data) {
                                alert(data.responseText)
//                                            alertBox()
                            }
                        })
                    })
                }, error: function(e, f) {
                    alertBox()
                    form_div.html(clist)
                }
            })
        })
    }
}

function createForm(form_div, group_id, data) {
    var c = 0
    if (group_id == null) {
        c = 1
    }
    var form
    if(c==1)
        form= $("<form style='margin-left:20px;margin-right:20px;' id='group_create_form'>")
    else
        form= $("<form style='margin-left:20px;margin-right:20px;' id='group_manage_form'>")

    var group_name_label = $("<label for='group_name'>")
    group_name_label.html("Group Name")
    form.html(group_name_label)
    form.append("<br>")
        var group_name
    if (c == 0)
        group_name = $("<input type='text' value='" + data.name + "' id='group_name' name='group_name' placeholder='Enter the group name' required>")
    else
    group_name = $("<input type='text' id='group_name' name='group_name' placeholder='Enter the group name' required>")
    form.append(group_name)
    form.append("<br><br>")

    var group_type_label = $("<label>")
    group_type_label.html("Group Type")
    form.append(group_type_label)
    form.append("<br>") 
    var table = $("<table style='width:100%' id='gtype_table'>")
    form.append(table)
    var tr0 = $("<tr>")
    var td0 = $("<td>")
    table.append(tr0)
    tr0.append(td0)
    var family_label = $("<label for='family_radio'>")
    family_label.html("Family")
    var family_radio
        if (c == 0) {
            if (data.type == "family")
            family_radio = $("<input type='radio' id='family_radio' name='group_type' value='family' checked>")
        else
        family_radio = $("<input type='radio' id='family_radio' name='group_type' value='family'>")
    } else {
    family_radio = $("<input type='radio' id='family_radio' name='group_type' value='family'>")
    }
    td0.append(family_radio)
    td0.append(family_label)
    var td1 = $("<td>")
    tr0.append(td1)
    var friends_label = $("<label for='friend_radio'>")
    friends_label.html("Friends")
    var friends_radio
        if (c == 0) {
            if (data.type == "friend")
            friends_radio = $("<input type='radio' id='friend_radio' name='group_type' value='friend' checked>")
        else
        friends_radio = $("<input type='radio' id='friend_radio' name='group_type' value='friend'>")
    } else {
    friends_radio = $("<input type='radio' id='friend_radio' name='group_type' value='friend'>")
    }
    td1.append(friends_radio)
    td1.append(friends_label)
    var td2 = $("<td>")
    tr0.append(td2)
    var office_label = $("<label for='office_radio'>")
    office_label.html("Office")
    var office_radio
        if (c == 0) {
            if (data.type == "office")
            office_radio = $("<input type='radio' id='office_radio' name='group_type' value='office' checked>")
        else
        office_radio = $("<input type='radio' id='office_radio' name='group_type' value='office'>")
    } else {
    office_radio = $("<input type='radio' id='office_radio' name='group_type' value='office'>")
    }
    td2.append(office_radio)
    td2.append(office_label)
    var tr1 = $("<tr>")
    table.append(tr1)
    var td3 = $("<td>")
    tr1.append(td3)
    var school_label = $("<label for='school_radio'>")
    school_label.html("School")
    var school_radio
        if (c == 0) {
            if (data.type == "school")
            school_radio = $("<input type='radio' id='school_radio' name='group_type' value='school' checked>")
        else
        school_radio = $("<input type='radio' id='school_radio' name='group_type' value='school'>")
    } else {
    school_radio = $("<input type='radio' id='school_radio' name='group_type' value='school'>")
    }
    td3.append(school_radio)
    td3.append(school_label)
    var td4 = $("<td>")
    tr1.append(td4)
    var interest_label = $("<label for='interest_radio'>")
    interest_label.html("Interest")
    var interest_radio
        if (c == 0) {
            if (data.type == "interest")
            interest_radio = $("<input type='radio' id='interest_radio' name='group_type' value='interest' checked>")
        else
        interest_radio = $("<input type='radio' id='interest_radio' name='group_type' value='interest'>")
    } else {
    interest_radio = $("<input type='radio' id='interest_radio' name='group_type' value='interest'>")
    }
    td4.append(interest_radio)
    td4.append(interest_label)
    var td5 = $("<td>")
    tr1.append(td5)
    var other_label = $("<label for='other_radio'>")
    other_label.html("Other")
    var other_radio
        if (c == 0) {
            if (data.type == "other")
            other_radio = $("<input type='radio' id='other_radio' name='group_type' value='other' checked>")
        else
        other_radio = $("<input type='radio' id='other_radio' name='group_type' value='other'>")
    } else {
    other_radio = $("<input type='radio' id='other_radio' name='group_type' value='other'>")
    }
    td5.append(other_radio)
    td5.append(other_label)
    form.append("<br>")

    var block_label = $("<label for='block'>")
    block_label.html("Block friends in this group")
    var block
        if (c == 0) {
            if (data.blocked == 0)
            block = $("<input type='checkbox' id='block' name='block'>")
        else
        block = $("<input type='checkbox' id='block' name='block' checked>")
    } else {
    block = $("<input type='checkbox' id='block' name='block'>")
    }
    form.append(block)
    form.append(block_label)
    form.append("<br>")

    var sharing_label = $("<label for='sharing'>")
    sharing_label.html("Don't share private posts unless I tag")
    var sharing
        if (c == 0) {
            if (data.shared == 0)
            sharing = $("<input type='checkbox' id='sharing' name='sharing'>")
        else
        sharing = $("<input type='checkbox' id='sharing' name='sharing' checked>")
    } else {
    sharing = $("<input type='checkbox' id='sharing' name='sharing'>")
    }
    form.append(sharing)
    form.append(sharing_label)
    form.append("<br>")

    var suggest_label = $("<label for='suggest'>")
    suggest_label.html("Suggest friends in this group")
    var suggest
        if (c == 0) {
            if (data.suggest == 0)
            suggest = $("<input type='checkbox' id='suggest' name='suggest' >")
        else
         suggest = $("<input type='checkbox' id='suggest' name='suggest' checked=true checked>")
    } else {
     suggest = $("<input type='checkbox' id='suggest' name='suggest' checked=true checked>")
    }
    form.append(suggest)
    form.append(suggest_label)
    form.append("<br><br>")

    form_div.html(form)
    group_name.width(form.width() - 20)
    $("label").addClass("group_label")
    $("#gtype_table tr td").width("33%") 
        if (c == 0) {         var save = $("<input type='button' id='save' class='bbutton' value='Save' style='width:80px;'>")
        form.append(save)
        return save
    } else {
        var create = $("<input type='button' id='create' class='bbutton' value='Create' style='width:80px;'>")
        form.append(create)
return create
    }
}