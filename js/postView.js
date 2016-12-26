function postView(args) {
    var id = args[0]
    var cvr
    $.ajax({
        url: "manager/PostManager.php",
        cache: true,
        type: "get",
        dataType: "json",
        data: "req=get_post_detail&id=" + id + "&uid=" + user_id,
        beforeSend: function() {
            cvr = $("<div class='white_overlay'>")
            cvr.html("<center><img src='img/massive_ajax_loader.gif' style='margin-top:30px;'></center>")
            $("body").prepend(cvr)
        },
        success: function(data) {
            cvr.remove()
            buildPage(data, cvr, args[1]);
        },
        error: function(e, f) {
            cvr.remove()
            alertBox("There was some problem loading the post. Please try again later.")
        }
    })
}

function getFormattedTime(timeFormat) {
    var time_array = timeFormat.split(":")
    var hour = time_array[0]
    var minute = time_array[1]
    var desig = "AM"
    if (hour > 12) {
        hour = hour - 12
        desig = "pm"
    }
    return hour + ":" + minute + " " + desig
}

function buildPage(data, cvr, ex) {
    var post = data.main_post;
    var set_posts = data.set_post_array;
    var tag_posts = data.tag_post_array;
    var id = post.id;
    var cur_url = document.URL;
    var cur_title = document.getElementsByTagName("title")[0].innerHTML;
    var page, poster_id = post.user_id, first_name = post.first_name, last_name = post.last_name, profile_pic = post.profile_pic, post_title = post.title, post_description = post.description
    var set = post.set, date = post.date, time = post.time, i, set_posts_img = [], tag_posts_img = []
    history.pushState({},post.title,"post.php?id="+post.id);
    if (profile_pic == null || profile_pic.length == 0) {
        profile_pic = "img/blur_default_profile_pic.jpg"
    }
    profile_pic = $("<img class='mt1 ml1 fl' style='width:40px;'>").attr("src", profile_pic)

    for (i = 0; i < set_posts.length; i++) {
        if (set_posts[i].width * 1 > set_posts[i].height * 1) {
            set_posts_img[i] = $("<img style='height:60px;' id='" + set_posts[i].id + "'>")
        } else {
            set_posts_img[i] = $("<img style='width:60px;' id='" + set_posts[i].id + "'>")
        }
        set_posts_img[i].attr("src", set_posts[i].src)
    }
    for (i = 0; i < tag_posts.length; i++) {
        if (tag_posts[i].width * 1 > tag_posts[i].height * 1) {
            tag_posts_img[i] = $("<img style='height:60px;' id='" + tag_posts[i].id + "'>")
        } else {
            tag_posts_img[i] = $("<img style='width:60px;' id='" + tag_posts[i].id + "'>")
        }
        tag_posts_img[i].attr("src", tag_posts[i].src)
    }

    page = new Box("post_" + id, 95, 80)
    page.createOverlay(1)
    page.no_head = true
    page.entry_animation = ["book_open", "left", 300]

    var main_body = page.createBox()
    var main_body_height = main_body.height()

    if (post.postType == "photo" || (post.postType == "link" && post.url_content_type == "photo")) {
        var img = $("<img>")
        img.attr("src", post.src)
        var arena = $("<div class='arena fl'>")
        arena.height(main_body_height)
        main_body.html(arena)
        arena.fitImage(img, post.width, post.height, "both")
    } else if (post.postType == "video" || (post.postType == "link" && post.url_content_type == "video")) {
        var embed = $("<embed>")
        img = $("<img>")
        img.attr("src", post.preview)
        embed.attr({
            "src": post.src + "?autoplay=1",
            "wmode": "transparent",
            "allowfullscreen": "true",
            "type": "application/x-shockwave-flash",
            "background": "black",
            "width": "100%",
            "height": 0.3 * $(this).width(),
        })
        var arena = $("<div class='arena fl'>")
        arena.height(main_body_height)
        main_body.html(arena)
        arena.html(embed)
        embed.css("margin-top", (arena.height() / 2) - (embed.height() / 2))
    }

    var info_arena = $("<div class='fr' style='overflow-y:auto;overflow-x:hidden;'>")
    info_arena.width(main_body.width() - arena.width() - 20)
    main_body.append(info_arena)

    var poster_info_div = $("<div class='mt1' style='width:100%;display:table;'>")
    info_arena.append(poster_info_div)
    var cross = $("<span class='box_close' style='margin:0 10px;'>")
    cross.html("X")
    poster_info_div.append(profile_pic)
    poster_info_div.append(cross)

    cross.click(function() {
        history.pushState({},cur_title,cur_url);
        page.closeBox()
    })

    profile_pic.addClass("rounded")
    poster_info_div.append("<div class='fl' style='margin-top:5px;margin-left:5px;'><b><a href='profile.php?id=" + post.user_id + "'>" + first_name + " " + last_name + "</a></b><br><span style='color:#999;font-family:Calibri;font-size:12px;'>" + getStraightDate(post.date) + " | " + getFormattedTime(post.time) + "</span></div>")

    var ttl = $("<div class='mt1 ml1' style='width:98%;color:#555;font-size:18px;font-family:Calibri;'>")
    ttl.html(decorateWithLinks(unrenderHTML(post_title)))
    info_arena.append(ttl)

    var desc = $("<div class='ml1' style='width:98%;color:#444;font-size:12px;font-family:Calibri;'>")
    var pd = (post_description != null) ? post_description : "";
    if (pd.length > 300)
        pd = pd.substr(0, 297) + "..."
    if (post.postType == "link") {
        var link = $("<a style='font-size:18px' href='" + post.url + "' target='_blank'>")
        link.html(post.url);
        desc.html(link)
        desc.append("<br>")
    }
    desc.append(decorateWithLinks(unrenderHTML(pd)));
    info_arena.append(desc);

    var tag_area = $("<div class='ml1 mt1' style='width:98%;display:table;margin-left:5px;'>")
    var tag_length = (post.tags).length
    for (i = 0; i < tag_length; i++) {
        var tagdiv
        if (i > 10)
            tagdiv = $("<div class='tag cp' style='display:none'>")
        else
            tagdiv = $("<div class='tag cp'>")
        var hi = $("<input type='hidden' value='" + post.tags[i].id + "'>")
        tagdiv.html("<span class='val'>" + post.tags[i].name + "</span>")
        tagdiv.append(hi)
        var tag_anchor = $("<a href='tag.php?id=" + post.tags[i].id + "'>")
        tag_anchor.html(tagdiv)
        tag_area.append(tag_anchor)
        tagdiv.hover(function() {
            $(this).addClass("tag_hover")
        }, function() {
            $(this).removeClass("tag_hover")
        })
    }
    info_arena.append(tag_area)

    var op_board = $("<div class='ml1' style='width:98%;display:table;margin-top:10px;margin-bottom:-10px;'>")
    info_arena.append(op_board)
    op_board.Like(id, "post", post.activity.user_liked, null, "dark")
    op_board.Share(id, post.postType, [img.clone(), post.width, post.height], "dark")

    var menu_div = $("<div class='ml1 mt1' style='width:50%;display:table'>")
    var menu = $("<ul class='hori_menu' id='post_tabs'>")
    var assoc_posts = $("<li id='apt' class='ctb'>")
    assoc_posts.html("Posts")
    var post_activity = $("<li id='at'>")
    post_activity.html("Activity")
    menu.html(assoc_posts)
    menu.append(post_activity)
    menu_div.html(menu)
    info_arena.append(menu_div)
    menu.children("li").click(function() {
        if ($(this).hasClass("ctb")) {
        }
        else {
            $(".ctb").removeClass("ctb")
            $(this).addClass("ctb")
            var id = $(this).attr("id")
            if (id == "apt") {
                showAssosiatedPosts(info_arena, set_posts_img, tag_posts_img)
            }
            else if (id == "at") {
                showActivity(info_arena, post.activity)
            }
        }
    })
    showAssosiatedPosts(info_arena, set_posts_img, tag_posts_img)

    function showAssosiatedPosts(info_arena, set_posts_img, tag_posts_img) {
        $("#activity_area").remove()
        //Other set posts
        info_arena.append("<div class='pwin_title'>Other posts from this set</div>")
        var set_post_window = $("<div class='ml1 pwin' style='position:relative;'>")
        info_arena.append(set_post_window)
        set_post_window.width(info_arena.width() - 50)
        set_post_window.sp_carousel(set_posts_img, 9, 3)
        set_post_window.find("img").click(function() {
            alert($(this).attr("id"))
        })

        //Interest Tagged posts
        info_arena.append("<div class='pwin_title'>Similar tagged posts</div>")
        var similar_post_window = $("<div class='ml1 pwin' style='position:relative;'>")
        info_arena.append(similar_post_window)
        similar_post_window.width(info_arena.width() - 50)
        similar_post_window.sp_carousel(tag_posts_img, 9, 3)
    }

    function showActivity(info_arena, post_activity) {
        $(".pwin").remove()
        $(".pwin_title").remove()
        var activity_area = $("<div style='width:90%;display:table' id='activity_area'>")
        var post_stats = $("<div style='width:100%;'>")
        post_stats.html("<ul class='linear_list' style='margin:10px;'><li>" + post_activity.no_of_likes + " Likes</li><li>" + post_activity.no_of_comments + " Comments</li><li>" + post_activity.no_of_shares + " Shares</li></ul>")
        post_stats.find("li").css({
            "font-family": "Calibri",
            "margin-right": "10px",
            "font-size": "15px"
        })
        activity_area.html(post_stats)
        info_arena.append(activity_area)

        var comments_area = $("<div style='width:70%;margin-left:10px;'>"), i
        for (i = 0; i < post_activity.comments.length; i++) {
            var comment_div = $("<div id='" + post_activity.comments[i].id + "' style='display:table;background:#f0f0f0;border:1px solid white;padding:5px;border-radius:3px;width:100%;font-family:Calibri;;font-size:15px;'>")
            var cpic_div = $("<div style='width:30px;height:30px;border-radius:3px;float:left;'>")
            comment_div.html(cpic_div);
            var cpic = $("<img src='" + post_activity.comments[i].profile_pic + "' style='width:100%;height:100%;border-radius:2px;'>")
            cpic_div.html(cpic)
            var comment_content_div = $("<div style='margin-left:35px;'>")
            comment_content_div.html("<a href='profile.php?id=" + post_activity.comments[i].id + "' class='fl' style='font-weight:bold;'>" + post_activity.comments[i].user_name + "</a>")
            var cdiv = $("<div style='word-wrap:break-word;'>")
            comment_content_div.append(cdiv)
            cdiv.html("&nbsp;&nbsp;" + post_activity.comments[i].comment)
            comment_div.append(comment_content_div)
            comments_area.append(comment_div)
        }
        activity_area.append(comments_area)

        var post_comment_div = $("<div style='width:70%;margin-left:10px;'>")
        var textarea = $("<textarea style='width:100%;resize:none;height:35px;border-radius:3px;padding-top:5px;margin-top:10px;' placeholder='Comment'>")
        post_comment_div.html(textarea)
        activity_area.append(post_comment_div)
        textarea.elastic()
        textarea.keyup(function(e) {
            if (e.keyCode == 13) {
                var ta = $(this)
                var post_id = info_arena.parent().parent().attr("id").substr(5)
                var comment = $.trim(ta.val())
                $.ajax({
                    url: "manager/PostManager.php",
                    type: "get",
                    data: "req=post_comment&post_id=" + post_id + "&comment=" + comment + "&type=text&user_id=" + user_id,
                    beforeSend: function() {
                        ta.parent().append("<center><img src='img/ajax_loader_horizontal.gif' style='width:20px;'></center>")
                        ta.hide()
                    },
                    success: function(r) {
                        if (r == -1) {
                            alertBox("Some error occured. Please try again later.")
                            ta.show()
                            ta.parent().children("center").remove()
                        } else {
                            ta.parent().children("center").remove()
                            ta.show()
                            ta.val("")

                            var comment_div = $("<div id='" + r + "' style='display:table;background:#f0f0f0;border:1px solid white;padding:5px;border-radius:3px;width:100%;font-family:Calibri;;font-size:15px;'>")
                            var cpic_div = $("<div style='width:30px;height:30px;border-radius:3px;float:left;'>")
                            comment_div.html(cpic_div);
                            var cpic = $("<img src='" + blur_profile_pic.attr("src") + "' style='width:100%;height:100%;border-radius:2px;'>")
                            cpic_div.html(cpic)
                            var comment_content_div = $("<div style='margin-left:35px;'>")
                            comment_content_div.html("<a href='profile.php?id=" + r + "' class='fl' style='font-weight:bold;'>" + user_name + "</a>")
                            var cdiv = $("<div style='word-wrap:break-word;'>")
                            comment_content_div.append(cdiv)
                            cdiv.html("&nbsp;&nbsp;" + comment)
                            comment_div.append(comment_content_div)
                            comments_area.prepend(comment_div)
                        }
                    }, error: function(e) {
                        alertBox("Some error occured. Please try again later.")
                        ta.show()
                        ta.parent().children("center").remove()
                    }
                })
            }
        })
    }
}