var window_width = screen.width
var window_height = screen.height
var loaded_script = []
var tag_list = [], tag_key = []
var user_sets = [], category_list = []
var Tags = function() {
    var id
    var name
    var popularity
}
var Friend = function() {
    var id
    var uid
    var name
    var profile_pic
    var req_type
}
var freq = [], pfr = 0, sfr = 0
var user_agent = navigator.userAgent
var browser
if (user_agent.toLowerCase().indexOf("chrome") > -1) {
    browser = 'chrome';
} else if (user_agent.toLowerCase().indexOf("safari") > -1) {
    browser = 'safari'
} else if (user_agent.toLowerCase().indexOf("opera") > -1) {
    browser = 'opera'
} else if (user_agent.toLowerCase().indexOf("firefox") > -1 || user_agent.toLowerCase().indexOf("netscape") > -1) {
    browser = 'mozilla'
} else if (user_agent.toLowerCase().indexOf("msie") > -1) {
    browser = 'msie'
}

getUpdates();

$(function() {

    var dd = new DropDown($('#dd'));

    $(document).click(function() {
        // all dropdowns
        $('.wrapper-dropdown-5').removeClass('active');
        $("#update_list").hide();
        $("#category_menu").remove();
    });

});

function DropDown(el) {
    this.dd = el;
    this.initEvents();
}
DropDown.prototype = {
    initEvents: function() {
        var obj = this;

        obj.dd.on('click', function(event) {
            $(this).toggleClass('active');
            event.stopPropagation();
        });
    }
}

$(document).ready(function() {
    if ($("#head_menu").length > 0 && $("#dock_div").length > 0) {
        $("#head_menu").css("min-width", screen.width)
        $("#dock_div").css("min-width", 0.058 * screen.width)
        getRequests()

        if ($("#dd").length > 0) {
            $("#vp a").attr("href", "profile.php?id=" + user_id)
            $("#dd").prepend("<table><tr><td class='uimg'></td><td style='padding-left:5px;'>" + user_name + "</td></tr></table>")
            $(".uimg").html(blur_profile_pic)
            blur_profile_pic.width(30)
            blur_profile_pic.height(30)
        }
    }

    $("#nd").click(function() {
        $("#update_list").show()
        event.stopPropagation();
    })
    $("#update_list").click(function() {
        $("#update_list").show()
        event.stopPropagation();
    })
    
    
    $("#cd").click(function() {
//        $("#category_menu").show("slide", {direction: "up"}, 200)
        event.stopPropagation();
    })
    $("#catgory_menu").click(function() {
//        $("#catgory_menu").show("slide", {direction: "up"}, 200)
        event.stopPropagation();
    })

    if ($("#dock_div").length > 0) {
        $("div#dock_div").css("margin-top", 0.15 * window_height)
        $("ul#dock li").css({
            "padding-top": 0.00912 * window_height,
            "padding-bottom": 0.0208 * window_height
        })
        $("#profile_dock a").attr("href", "profile.php?id=" + user_id)
        $("#vf_dock a").attr("href", "friends.php?id=" + user_id)
        $("#photos a").attr("href", "setsgallery.php?id=" + user_id)
        $("#intPage a").attr("href", "interests.php?id=" + user_id)

        var countdown
        $("#dock_div").show().delay(1500).hover(function() {
            clearTimeout(countdown)
        })
        countdown = setTimeout(function() {
            $("#dock_div").animate({
                "margin-left": "-5%"
            }, 300)
        }, 1500)
    }

    $("ul#dock li a").mouseover(function() {
        var src = $(this).find("img").attr("src")
        src = src.substring(0, src.indexOf("B")) + src.substring(src.indexOf("B") + 1)
        $(this).find("img").attr("src", src)
    })

    $("ul#dock li a").mouseout(function() {
        var src = $(this).find("img").attr("src")
        src = src.substring(0, src.indexOf(".")) + "B." + src.substring(src.indexOf(".") + 1)
        $(this).find("img").attr("src", src)
    })

    $("ul#dock li").click(function() {
        $(".sub_level").hide()
        var scrollTop = $(window).scrollTop(),
                elementOffset = $(this).offset().top,
                distance = (elementOffset - scrollTop) / 1.2;
        $(this).children(".sub_level").css("top", distance);
        $(this).children(".sub_level").show();
    })

    $("#dock_div").hover(function() {
        $("#dock_div").animate({
            "margin-left": "0px"
        }, 300)
        clearTimeout(countdown)
    }, function() {
        countdown = setTimeout(function() {
            $("#dock_div").animate({
                "margin-left": "-5%"
            }, 300)
            $(".sub_level").hide("slide", {
                direction: "left"
            }, 300)
        }, 800)
    })

    $(".sla").hover(function() {
        $(this).children("a").css("color", "white")
    }, function() {
        $(this).children("a").css("color", "#444")
    })

    $("ul#dock li").click(function(e) {
        if ($(this).attr("id") == "friends") {
            e.stopPropagation()
            return false
        }
    });

    $("#pfr_dock").click(function() {
        openRequestBox("pending");
    })

    $("#sfr_dock").click(function() {
        openRequestBox("sent");
    })

    $("#vf_dock").click(function() {
        window.location = "friends.php?id=" + user_id
    })

    $(".hm_op").hover(function() {
        var img = $(this).find("img.nicon")
        var src = img.attr("src")
        src = src.substr(0, src.indexOf(".")) + "_hover.png"
        img.attr("src", src)
    }, function() {
        var img = $(this).find("img.nicon")
        var src = img.attr("src")
        src = src.substr(0, src.indexOf("_")) + ".png"
        img.attr("src", src)
    })

    //Datepicker
    if ($("input[type='datepicker']").length > 0) {
        var current_year = (new Date).getFullYear()
        $("input[type='datepicker']").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-MM-yy',
            showAnim: 'slideDown',
            yearRange: '1900:' + current_year,
            onSelect: function() {
                var dateAsObject = $(this).datepicker('getDate')
                var date = dateAsObject.getDate()
                var month = dateAsObject.getMonth()
                var year = dateAsObject.getYear() + 1900
                var output = date + " " + getMonthName(month) + ", " + year
                $("input[type='datepicker']").val(output)
            }
        })
        $("input[type='datepicker']").attr("readonly", true)
    }

    //Nemo Search
    $("#nemo_search").keyup(function(e) {
        if ($("#searchview").length > 0) {
        } else {
            if (!((e.keyCode >= 48 && e.keyCode <= 90) || e.keyCode == 32 || e.keyCode == 8 || e.keyCode == 13 || (e.keyCode >= 96 && e.keyCode <= 111) || (e.keyCode >= 186 && e.keyCode <= 192 || (e.keyCode >= 219 && e.keyCode <= 222)))) {
                return;
            }
            var searchview = $("<div id='searchview'>");
            $("body").append(searchview);
        }
    })
    $("#nemo_search").smartSearch({
        listSuggestion: false,
        listThumbnails: true,
        dataSourceURL: "searchManager.php",
        dataSourceReturnType: "json",
        dataSourceParameters: "req=main",
        textParameter: "q",
        autoComplete: false,
        approach: "time_interval",
        callback: function(data) {
            $("#searchview").html("<p id='smsg' style='font-weight:bold;font-size:25px;color:#DCDADA;margin:10px 20px;margin-bottom:0;'>Press Esc to exit</p>");
            var mainview = $("<div id='mainview' class='rview'>");
            $("#searchview").append(mainview);
            for (var head in data) {
                if (data[head].length > 0) {
                    var result_block;
                    if (head != "Tags")
                        result_block = $("<div style='float:left;padding:20px;width:50%;width:calc(50% - 2.5em);' id='result_block'>");
                    else
                        result_block = $("<div style='float:left;padding:20px;width:40%' id='result_block'>");
                    mainview.append(result_block)
                    var heading = $("<h1 style='font-weight:lighter;font-size:45px;'>").html(head)
                    result_block.html(heading);

                    var firstresult;
                    if (head != "Tags")
                        firstresult = $("<div class='firstresult'>")
                    if (head == "People") {
                        firstresult = $("<div class='firstresult' style=\"background-image:url('" + data[head][0].profile_pic + "')\">");
                        firstresult.html("<div class='grad'></div>");
                        firstresult.append("<p class='restext'>" + data[head][0].name + "</p>");
                    } else if (head == "Posts") {
                        firstresult = $("<div class='firstresult' style=\"background-image:url('" + data[head][0].src + "')\">")
                        firstresult.html("<div class='grad'></div>");
                        var title = data[head][0].title;
                        if (title.length > 40)
                            title = title.substr(0, 37) + "...";
                        firstresult.append("<p class='restext'>" + title + "</p>");
                    }
                    if (head != "Tags")
                        result_block.append(firstresult);

                    var i;
                    for (i = 1; i < data[head].length; i++) {
                        var searchresult;
                        if (head == "People") {
                            searchresult = $("<div class='searchresult' style=\"background-image:url('" + data[head][i]["profile_pic"] + "');background-size:cover;\">")
                            searchresult.html("<div class='grad'></div>");
                            searchresult.append("<p class='restext'>" + data[head][i].name + "</p>");
                        }
                        else if (head == "Posts") {
                            searchresult = $("<div class='searchresult' style=\"background-image:url('" + data[head][i].src + "');background-size:cover;\">")
                            searchresult.html("<div class='grad'></div>");
                            var title = data[head][i].title;
                            if (title.length > 20)
                                title = title.substr(0, 17) + "...";
                            searchresult.append("<p class='restext'>" + title + "</p>");
                        }
                        else if (head == "Tags") {
                            searchresult = $("<div class='listresult'>")
                            searchresult.html("<img src='img/tag_icon.png' style='height:40px;float:left;'>&nbsp;&nbsp;" + data[head][i - 1].name)
                        }
                        result_block.append(searchresult);
                    }
                    if (head == "Tags") {
                        var searchresult;
                        searchresult = $("<div class='listresult'>")
                        searchresult.html("<img src='img/tag_icon.png' style='height:40px;float:left;'>&nbsp;&nbsp;" + data[head][i - 1].name)
                        result_block.append(searchresult)

                        var moreresult = $("<div class='listresult mres' style='color:#14b30e;' id='Tags_more_result' onclick='showMoreSearch(this)'>")
                        moreresult.html("<img src='img/tag_icon.png' style='height:40px;float:left;'>&nbsp;&nbsp;View more results...")
                        result_block.append(moreresult)
                    } else {
                        var moreresult;
                        moreresult = $("<div class='searchresult mres' id='" + head + "_more_result' onclick='showMoreSearch(this)'>")
                        moreresult.html("<center><div style='color:white;font-size:25px;margin-top:58px;'>More results</div></center>");
                        result_block.append(moreresult)
                    }
                }
            }
        }
    })
})

function showMoreSearch(e) {
    $("#mainview").fadeOut("500", function() {
        var id = $(e).attr("id").substring(0, $(e).attr("id").indexOf("_"));
        var moreview = $("<div id='" + id + "view' class='mview rview' style='padding:20px;width:100%;width:calc(100% - 2.5em);display:none;'>");
        var back = $("<img src='img/back.png' style='float:left;margin-right:20px;cursor:pointer;'>")
        moreview.html(back);
        back.click(function() {
            $(".mview").fadeOut("500", function() {
                $("#mainview").fadeIn("500");
            })
        })
        var heading = $("<h1 style='font-weight:lighter;font-size:45px;'>").html(id)
        moreview.append(heading);
        if (id == "Posts" || id == "People") {
            var menudiv = $("<div style='display:table;margin-top:10px;'>");
            moreview.append(menudiv);
            var menu = $("<ul class='hori_menu'>");
            menudiv.html(menu);
            if (id == "Posts") {
                menu.html("<li class='ctb'>All</li><li>Photos</li><li>Videos</li><li>Web Links</li><li>Panorama</li><li>Places</li><li>Events</li>");
            } else if (id == "People") {
                menu.html("<li class='ctb'>All</li><li>Friends</li><li>Followers</li><li>Following</li>");
            }
        }
        var resultdiv = $("<div class='rview' style='height:auto;'>");
        resultdiv.html("<center><img src='img/ajax_loader_horizontal.gif'></center>");
        $("#searchview").append(moreview);
        moreview.fadeIn("500");
        //getresults
    })
}

function getRequests() {
    $.ajax({
        url: "manager/FriendManager.php",
        type: "get",
        cache: false,
        dataType: "json",
        data: "req=get_requests&user_id=" + user_id,
        success: function(data) {
            var i, data_length = data.length
            pfr = 0, sfr = 0
            freq = []
            for (i = 0; i < data_length; i++) {
                freq[i] = new Friend()
                freq[i].req_type = data[i].req_type
                if (data[i].req_type == "pending")
                    pfr++
                else if (data[i].req_type == "sent")
                    sfr++
                freq[i].id = data[i].id
                freq[i].name = data[i].name
                freq[i].uid = data[i].uid
                freq[i].profile_pic = data[i].profile_pic
            }
        }
    })
}

function PostTile(detail, supported_options) {
    var id = detail.id
    var set_id = detail.set_id
    var parent_type = detail.parent_type
    var parent_user_id = detail.parent_user_id
    var parent_user_name = detail.parent_user_name
    var share_id = detail.share_id
    var postType = detail.postType
    var title = detail.title
    var description = detail.description
    var share_text = detail.share_text
    var src = detail.src
    var url = detail.url
    var url_content_type = detail.url_content_type
    var width = detail.width
    var height = detail.height
    var user_id = detail.user_id
    var profile_pic = detail.profile_pic
    var user_name = detail.user_name
    var likes = detail.likes
    var shares = detail.shares
    var comments = detail.comments
    var sharable = detail.sharable
    var date = detail.date
    var time = detail.time
    var user_liked = detail.user_liked
    //supported options
    var share_button = true;
    var like_button = true;
    var comment_button = true;
    var link_click = true;
    if (supported_options != null) {
        share_button = supported_options[2];
        like_button = supported_options[0];
        comment_button = supported_options[1];
        link_click = supported_options[3];
    }

    var element
    if (postType == "photo" || postType == "video" || (postType == "share" && (parent_type == "photo" || parent_type == "video"))) {
        element = $("<img class='pimg'>")
        element.attr("src", src)
    } else if (postType == "link" || (postType == "share" && parent_type == "link")) {
        if (url_content_type == "photo") {
            element = $("<img class='pimg'>")
            element.attr("src", src)
        }
    }

    this.arrangeTile = function(e, n, direction, share_callback) {
        var id = this.getId()
        var set_id = this.getSet_id()
        var parent_type = this.getParent_type()
        var parent_user_id = this.getParent_user_id()
        var parent_user_name = this.getParent_user_name()
        var share_id = this.getShare_id()
        var tile = $("<div>")
        var el = this.getElement()
        var url = this.getUrl()
        var url_content_type = this.getUrl_content_type()
        var title = this.getTitle()
        var description = this.getDescription()
        var share_text = this.getShare_text()
        var likes = this.getLikes()
        var comments = this.getComments()
        var shares = this.getShares()
        var user_liked = this.getUser_liked()
        var postType = this.getPostType()
        var user_id = this.getUser_id()
        var user_name = this.getUser_name()
        var profile_pic = this.getProfile_pic()
        var share_button = this.getShare_button()
        var like_button = this.getLike_button()
        var comment_button = this.getComment_button()
        var link_click = this.getLink_click()
        var tile_width = ((e.width()) / n) - (18)

        tile.attr("id", "post_" + id)
        tile.addClass("tile")

        var tile_title = $("<div>")
        tile_title.addClass("tile_title")
        if (title != null && title.length != 0) {
            title = decorateWithLinks(unrenderHTML(title))
            if (title.length > 50)
                title = title.substring(0, 47) + "..."
            tile_title.html("<div class='post_title'>" + title + "</div>")
            tile.html(tile_title)
        }

        tile.append(el)

        var tile_board = $("<div>")
        tile_board.addClass("tile_board")

        var tile_ppic_div = $("<div class='tile_ppic_div'>")
        var ppic = $("<img src='" + profile_pic + "'>")
        tile_ppic_div.html(ppic)
        tile_board.html(tile_ppic_div)
        tile_board.width(tile_width - 30)
        tile_board.append("<a href='profile.php?id=" + user_id + "' style='font-family:Calibri;font-size:16px;margin-left:10px;'>" + user_name + "</a>")
        if (postType == "share") {
            tile_board.append(" via " + "<a href='profile.php?id=" + parent_user_id + "' style='font-family:Calibri;font-size:16px;margin-left:10px;'>" + parent_user_name + "</a>")
        }
        tile_board.append("<br>")

        if (description != null && description.length != 0) {
            if (description.length > 50)
                description = description.substring(0, 47) + "..."
            if (url == null)
                tile_board.append("<div class='post_description'>" + decorateWithLinks(unrenderHTML(description)) + "</div>")
            else
                tile_board.append("<div class='post_description'>" + decorateWithLinks(/*url+"<br>"+*/unrenderHTML(description)) + "</div>")
        }
        tile.append(tile_board)

        if (share_button == true || like_button == true || comment_button == true) {
            var op_board = $("<div class='op_board'>")
            if (like_button == true)
                op_board.Like(id, "post", user_liked, null, null)
            if (comment_button == true)
                op_board.Comment(id, "post", null)
            if (share_button == true) {
                if (postType == "photo" || (postType == "share" && parent_type == "photo"))
                    op_board.Share(id, postType, [el.clone(), width, height], null, share_callback)
                else if (postType == "video" || (postType == "share" && parent_type == "video"))
                    op_board.Share(id, postType, [el.clone(), "480", "360"], null, share_callback)
                else if (postType == "link" || (postType == "share" && parent_type == "link")) {
                    op_board.Share(id, postType, [el.clone(), width, height], null, share_callback)
                }
            }
            tile.append(op_board)
        }
        if (direction == "append")
            e.append(tile)
        else
            e.prepend(tile)
        tile.width(tile_width)
        el.imagesLoaded(function() {
            if (direction == "append") {
                e.masonry({
                    itemSelector: '.tile',
                    isAnimated: true,
                    isFitWidth: true
                })
            } else {
                e.masonry('reload')
            }
        })

        tile_title.click(function() {
            scriptLoader("postView", "postView", [[id], [false]], 0, "")
        })
        tile_board.click(function() {
            scriptLoader("postView", "postView", [[id], [false]], 0, "")
        })
        el.click(function() {
            scriptLoader("postView", "postView", [[id], [false]], 0, "")
        })
    }

    this.setId = function(id1) {
        id = id1
    }
    this.setSet_id = function(set_id1) {
        set_id = set_id1
    }

    this.setParent_type = function(parent_type1) {
        parent_type = parent_type1
    }
    this.setParent_user_id = function(parent_user_id1) {
        parent_user_id = parent_user_id1
    }
    this.setParent_user_name = function(parent_user_name1) {
        parent_user_name = parent_user_name1
    }
    this.setElement = function(element1) {
        element = element1
    }
    this.setUrl = function(url1) {
        url = url1
    }
    this.setUrl_content_type = function(url_content_type1) {
        url_content_type = url_content_type1
    }
    this.setPostType = function(postType1) {
        postType = postType1
    }
    this.setTitle = function(title1) {
        title = title1
    }
    this.setDescription = function(description1) {
        description = description1
    }
    this.setShare_text = function(share_text1) {
        share_text = share_text1
    }
    this.setSrc = function(src1) {
        src = src1
        if (postType == "photo") {
            element = $("<img>")
            element.attr("src", src)
        } else if (postType == "video") {
            element = $("<embed>")
            element.attr({
                "src": src,
                "wmode": "transparent",
                "allowfullscreen": "true",
                "type": "application/x-shockwave-flash",
                "background": "black"
            })
        } else if (postType == "link") {
            if (url_content_type == "photo") {
                element = $("<img>")
                element.attr("src", src)
            }
        }
    }
    this.setWidth = function(width1) {
        width = width1
    }
    this.setHeight = function(height1) {
        height = height1
    }
    this.setUser_id = function(user_id1) {
        user_id = user_id1
    }
    this.setLikes = function(likes1) {
        likes = likes1
    }
    this.setShares = function(shares1) {
        shares = shares1
    }
    this.setComments = function(comments1) {
        comments = comments1
    }
    this.setSharable = function(sharable1) {
        sharable = sharable1
    }
    this.setDate = function(date1) {
        date = date1
    }
    this.setTime = function(time1) {
        time = time1
    }
    this.setUser_liked = function(user_liked1) {
        setUser_liked = user_liked1
    }
    this.setPostType = function(postType1) {
        postType = postType1
    }
    this.setUser_id = function(user_id1) {
        user_id = user_id1
    }
    this.setUser_name = function(user_name1) {
        user_name = user_name1
    }
    this.setProfile_pic = function(profile_pic1) {
        profile_pic = profile_pic1
    }
    this.setShare_button = function(share_button1) {
        share_button = share_button1;
    }
    this.setLike_button = function(like_button1) {
        like_button = like_button1;
    }
    this.setComment_button = function(comment_button1) {
        comment_button = comment_button1;
    }
    this.setLink_click = function(link_click1) {
        link_click = link_click1;
    }

    this.getId = function() {
        return id
    }
    this.getParent_user_name = function() {
        return parent_user_name
    }
    this.getParent_type = function() {
        return parent_type
    }
    this.getParent_user_id = function() {
        return parent_user_id
    }
    this.getSet_id = function() {
        return set_id
    }
    this.getShare_id = function() {
        return share_id
    }
    this.getElement = function() {
        return element
    }
    this.getUrl = function() {
        return url
    }
    this.getUrl_content_type = function() {
        return url_content_type
    }
    this.getPostType = function() {
        return postType
    }
    this.getTitle = function() {
        return title
    }
    this.getDescription = function() {
        return description
    }
    this.getShare_text = function() {
        return share_text
    }
    this.getSrc = function() {
        return src
    }
    this.getWidth = function() {
        return width
    }
    this.getHeight = function() {
        return height
    }
    this.getUser_id = function() {
        return user_id
    }
    this.getUser_name = function() {
        return user_name
    }
    this.getProfile_pic = function() {
        return profile_pic
    }
    this.getLikes = function() {
        return likes
    }
    this.getShares = function() {
        return shares
    }
    this.getComments = function() {
        return comments
    }
    this.getSharable = function() {
        return sharable
    }
    this.getDate = function() {
        return date
    }
    this.getTime = function() {
        return time
    }
    this.getUser_liked = function() {
        return user_liked
    }
    this.getPostType = function() {
        return postType
    }
    this.getShare_button = function() {
        return share_button;
    }
    this.getLike_button = function() {
        return like_button;
    }
    this.getComment_button = function() {
        return comment_button;
    }
    this.getLink_click = function() {
        return link_click;
    }
}

function AjaxRequest(url, cache, type, dataType, data_array) {
    var context = this, data = "", i
    for (i = 0; i < data_array.length; i++) {
        data += data_array[i][0] + "=" + encodeURIComponent(data_array[i][1]) + "&"
    }
    data = data.substr(0, data.length - 1)

    this.sendRequest = function() {
        if (dataType == null || dataType.length == 0) {
            $.ajax({
                url: url,
                cache: cache,
                type: type,
                data: data,
                beforeSend: function() {
                    if (context.beforeSend != null) {
                        var function_name = context.beforeSend[0]
                        //                        var args = context.beforeSend[1]
                        window[function_name](data)
                    }
                },
                success: function(html) {
                    if (context.success != null) {
                        var function_name = context.success[0]
                        //                        var args = context.success[1]
                        window[function_name](html)
                    }
                },
                complete: function() {
                    if (context.complete != null) {
                        var function_name = context.complete[0]
                        //                        var args = context.complete[1]
                        window[function_name]()
                    }
                },
                error: function(x, e, f) {
                    if (context.error != null) {
                        var function_name = context.error[0]
                        //                        var args = context.error[1]
                        window[function_name]([e, f])
                    } else {
                        alertBox()
                    }
                }
            })
        } else {
            $.ajax({
                url: url,
                cache: cache,
                type: type,
                dataType: dataType,
                data: data,
                beforesend: function() {
                    if (context.beforeSend != null) {
                        var function_name = context.beforeSend[0]
                        //                        var args = context.beforeSend[1]
                        window[function_name]()
                    }
                },
                success: function(html) {
                    if (context.success != null) {
                        var function_name = context.success[0]
                        //                        var args = context.success[1]
                        window[function_name](html)
                    }
                },
                complete: function() {
                    if (context.complete != null) {
                        var function_name = context.complete[0]
                        //                        var args = context.complete[1]
                        window[function_name]()
                    }
                },
                error: function(e, f) {
                    if (context.error != null) {
                        var function_name = context.error[0]
                        //                        var args = context.error[1]
                        window[function_name]([e, f])
                    } else {
                        alertBox()
                    }
                }
            })
        }

    }
}

function getMonthName(month) {
    var month_name = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    return month_name[(month - 1) * 1]
}

function getMonthNumber(month_name) {
    var month_name_array = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    var short_month_name_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    var month_number = $.inArray(month_name, month_name_array)
    if (month_number == -1) {
        month_number = $.inArray(month_name, short_month_name_array)
        if (month_number == -1) {
            return 0
        }
    }
    return month_number + 1
}

function getStraightDate(dateFormat) {
    var date_array = dateFormat.split("-")
    var date = date_array[2]
    var month = getMonthName(date_array[1])
    var year = date_array[0]
    return date + " " + month + ", " + year
}

function validateEmailAddress(email_id) {
    email_id = email_id.trim()
    if (email_id.length == 0)
        return false
    var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return reg.test(email_id)
}

function validatePhoneNumber(ph_no) {
    ph_no = ph_no.trim()
    if (ph_no.length == 0)
        return false
    var reg = /^\d+$/
    return reg.test(ph_no)
}

function checkUserAge(dd, mm, yyyy) {
    var valid_age = 13

    var current_year = (new Date).getFullYear()
    var current_month = (new Date).getMonth() + 1
    var current_date = (new Date).getDate()

    if (current_year - yyyy < valid_age) {
        return false
    } else if (current_year - yyyy > valid_age) {
        return true
    } else {
        if (current_month - mm < 0) {
            return false
        } else if (current_month - mm > 0) {
            return true
        } else {
            if (current_date - dd < 0) {
                return false
            } else if (current_date - dd >= 0) {
                return true
            }
        }
    }
}

function unrenderHTML(string) {
    return string.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, "&gt;")
}

function decorateWithLinks(string) {
    var exp = /(\b(https?|ftp|ftps|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return string.replace(exp, "<a href='$1'>$1</a>");
}

function centerFit(parent, childElementArray, margin, unit) {
    var i
    for (i = 0; i < childElementArray.length; i++) {
        if (unit == "px") {
            childElementArray[i].width(parent.width() - (2 * margin))
            childElementArray[i].css({
                "margin-left": margin
            })
        }
        else if (unit == "%") {
            childElementArray[i].width(parent.width() - (.02 * margin * parent.width()))
            childElementArray[i].css({
                "margin-left": margin * parent.width() / 100
            })
        }
    }
}

function ScreenSlider(screen_matrix) {
    var cord_x = 0, cord_y = 0
    var window_width = $(window).width()
    var window_height = $(window).height()
    this.slideTo = function(destination_element_div, duration) {
        var i, j, c = 0
        for (i = 0; i < screen_matrix.length; i++) {
            for (j = 0; j < screen_matrix[i].length; j++) {
                if (screen_matrix[i][j] == destination_element_div) {
                    c = 1
                    break
                }
            }
            if (c == 1) {
                break
            }
        }
        this.slideHorizontal(screen_matrix, cord_x, cord_y, j, duration / (Math.abs(cord_x - j)), window_width)
        this.slideVertical(screen_matrix, cord_x, cord_y, i, duration / (Math.abs(cord_y - i)), window_height)
        cord_x = j
        cord_y = i
    }

    this.slideHorizontal = function(screen_matrix, cord_x, cord_y, j, duration, window_width) {
        var context = this
        if (j > cord_x) {
            $("#" + screen_matrix[cord_y][cord_x]).animate({
                "margin-left": -window_width
            }, duration, "linear", function() {
                if (j >= cord_x + 1) {
                    context.slideHorizontal(screen_matrix, cord_x + 1, cord_y, j, duration, window_width)
                }
            })
        } else if (j < cord_x) {
            $("#" + screen_matrix[cord_y][cord_x - 1]).animate({
                "margin-left": 0
            }, duration, "linear", function() {
                if (j < cord_x - 1) {
                    context.slideHorizontal(screen_matrix, cord_x - 1, cord_y, j, duration, window_width)
                }
            })
        }
    }

    this.slideVertical = function(screen_matrix, cord_x, cord_y, i, duration, window_height) {
        var context = this
        if (i > cord_y) {
            $("#" + screen_matrix[cord_y][cord_x]).parent().animate({
                "margin-top": -window_height
            }, duration, "linear", function() {
                if (i >= cord_y + 1)
                    context.slideVertical(screen_matrix, cord_x, cord_y + 1, i, duration, window_height)
            })
        }
        else if (i < cord_y) {
            $("#" + screen_matrix[cord_y - 1][cord_x]).parent().animate({
                "margin-top": 0
            }, duration, "linear", function() {
                if (i < cord_y - 1)
                    context.slideVertical(screen_matrix, cord_x, cord_y - 1, i, duration, window_height)
            })
        }
    }
}

$.fn.sp_carousel = function(img_set, cols, rows) {
    var frames = [], k = -1, i, cnt = cols * rows, info_arena = $(this).parent()
    var caller_width = $(this).width(), caller_height = $(this).height()
    var per_width = (caller_width / cols) - 4
    for (i = 0; i < img_set.length; i++) {
        if (i % cnt == 0) {
            k++
            if (k == 0)
                frames[k] = $("<div id='frame_" + k + "' style='width:" + caller_width + "px;height:" + caller_height + "px;overflow:hidden;float:left;position:absolute;' class='current_frame'>")
            else
                frames[k] = $("<div id='frame_" + k + "' style='width:" + caller_width + "px;height:" + caller_height + "px;overflow:hidden;float:left;position:absolute;display:none;'>")
            $(this).append(frames[k])
        }
        var spost;
        spost = $("<div id='" + img_set[i].attr("id") + "' style='background-image:url(\"" + img_set[i].attr("src") + "\");width:" + per_width + "px;height:" + per_width + "px;' class='imgn'>")
        frames[k].append(spost)
        spost.children("img").hover(function() {
            $(this).fadeTo("fast", 0.5)
        }, function() {
            $(this).fadeTo("fast", 1)
        })
    }
    var left = $("<input type='button' value='Left'>")
    var right = $("<input type='button' value='Right'>")
    left.click(function() {
        var cf = $(".current_frame")
        var frame_number = cf.attr("id").substr(6)
        var nf = $("#frame_" + ((frame_number * 1) + 1))
        cf.removeClass("current_frame")
        cf.hide("slide", {
            direction: "left"
        }, 700)
        nf.show("slide", {
            direction: "right"
        }, 700)
        nf.addClass("current_frame")
    })
    right.click(function() {
        var cf = $(".current_frame")
        var frame_number = cf.attr("id").substr(6)
        var nf = $("#frame_" + (frame_number - 1))
        cf.removeClass("current_frame")
        cf.hide("slide", {
            direction: "right"
        }, 700)
        nf.show("slide", {
            direction: "left"
        }, 700)
        nf.addClass("current_frame")
    })
//    info_arena.append(left)
//    info_arena.append(right)
}

$.fn.photostack = function(pic1, pic2, pic3, current_class) {
    $(this).live({
        mouseenter: function() {
            if ($(this).find("img").hasClass(current_class)) {
                //attach the css class rotate1 , rotate2 and rotate3 to each image in the stack to rotate the images to specific degrees
                var $parent = $(this);
                $parent.find('img#' + pic1).addClass('rotate1');
                $parent.find('img#' + pic2).addClass('rotate2');
                $parent.find('img#' + pic3).addClass('rotate3');
                $parent.find('img#' + pic1).css("left", "150px");//reposition the last and first photo
                $parent.find('img#' + pic3).css("left", "50px");

            }
        },
        mouseleave: function() {
            $('img#' + pic1).removeClass('rotate1');
            $('img#' + pic2).removeClass('rotate2');
            $('img#' + pic3).removeClass('rotate3');
            $('img#' + pic1).css("left", "");
            $('img#' + pic3).css("left", "");
        }
    })
}

$.fn.multichoice = function(title, options, no_of_columns, width, max_open_height) {
    this.html(title + "<div class='down_arrow' style='float:right;margin-top:20px;' id='mc_" + $(this).attr("id") + "'></div>")
    if (width == null)
        width = "100%"
    $(this).width(width)
    $(this).addClass("multichoice")
    $(this).hover(function() {
        $("#mc_" + $(this).attr("id")).css("border-top-color", "#14b30e")
    }, function() {
        $("#mc_" + $(this).attr("id")).css("border-top-color", "#007dff")
    })
    this.click(function() {
        if ($(".multichoice_open").length > 0) {
            $(".multichoice_open").remove()
            $("#mc_" + $(this).attr("id")).css("border-top-color", "#007dff")
        }
        else {
            var outputString = "<div class='multichoice_open' id='mo_" + $(this).attr("id") + "'><table>"
            var i, j, k = 0
            var no_of_rows = options.length / no_of_columns
            if (options.length % no_of_columns != 0)
                no_of_rows++
            for (i = 0; i < no_of_rows; i++) {
                outputString += "<tr>"
                for (j = 0; j < no_of_columns; j++) {
                    if (k < options.length) {
                        if (title == options[k][1])
                            outputString += "<td class='mo_selected' id='mc_option" + options[k][0] + "'>" + options[k][1] + "</td>"
                        else
                            outputString += "<td id='mc_option" + options[k][0] + "'>" + options[k][1] + "</td>"
                        k++
                    } else
                        break
                }
                outputString += "</tr>"
            }
            outputString += "</table></div>"
            $(this).append(outputString)
            $("#mo_" + $(this).attr("id")).width(230 * no_of_columns)
            if (max_open_height != null) {
                $("#mo_" + $(this).attr("id")).height(max_open_height)
                $("#mo_" + $(this).attr("id")).css("overflow", "auto")
            }
        }
    })
}

$.fn.autoSuggest = function(purpose, text, width, placeholder, tagger) {
    $(".autosuggest").remove()
    var text_length = text.length
    if (text_length == 0) {
        $(".autosuggest").remove()
        return
    }
    var auto_suggest = $("<ul class='autosuggest'>")
    auto_suggest.width(width)
    var input = this
    if (purpose == "interest") {
        var list_length = tag_list.length, i
        for (i = 0; i < list_length; i++)
        {
            if ((tag_list[i].name).substr(0, text_length) == text) {
                var li = $("<li>")
                li.html("<input type='hidden' value='" + tag_list[i].id + "' id='as" + i + "'>")
                li.append("<span>" + tag_list[i].name + "</span>")
                auto_suggest.append(li)

                li.click(function() {
                    input.val($(this).children("span").html())
                    $(".autosuggest").remove()
                    addAsTag(input, placeholder, tagger, $(this).children("input").val())
                })
            }
        }
        input.parent().append(auto_suggest)
    }
}

function addAsTag(input, placeholder, tagger, id) {
    var tag = input.val().trim(), hi, i
    if (id == null) {
        var list_length = tag_list.length
        for (i = 0; i < list_length; i++)
        {
            if ((tag_list[i].name) == tag) {
                hi = $("<input type='hidden' value='" + tag_list[i].id + "'>")
                break
            }
        }
        if (i == list_length)
            hi = $("<input type='hidden' value=''>")
    } else {
        hi = $("<input type='hidden' value='" + id + "'>")
    }
    input.val("")
    var tagdiv = $("<div class='tag'>")
    tagdiv.html("<span class='val'>" + tag + "</span>")
    tagdiv.append(hi)
    var cantag = $("<span class='cantag'>")
    cantag.html("x")
    cantag.click(function() {
        tagdiv.remove()
        hi.remove()
        if ($(".cantag").length == 0) {
            input.attr("placeholder", placeholder)
        }
    })
    tagdiv.append(cantag)
    tagger.append(tagdiv)
    input.remove()
    tagger.append(input)
    input.removeAttr("placeholder")
    input.focus()

    var curposition = -1
    input.keyup(function(e) {
        var keyCode = e.keyCode
        if (keyCode == 38 || keyCode == 40) {
            var as = $(".autosuggest"), cnt = 0
            as.find("li").each(function() {
                cnt++
            })
            if (as) {
                if (keyCode == 38) {
                    if (curposition > 0)
                        curposition--
                    as.find("#as" + curposition).each(function() {
                        $(".autosuggest_li_hover").removeClass("autosuggest_li_hover")
                        $(this).addClass("autosuggest_li_hover")
                    })
                } else if (keyCode == 40) {
                    if (curposition < cnt)
                        curposition++
                    as.find("#as" + curposition).each(function() {
                        $(".autosuggest_li_hover").removeClass("autosuggest_li_hover")
                        $(this).addClass("autosuggest_li_hover")
                    })
                }
            }
        }
    })
}

$.fn.addTagger = function(id, placeholder, tagManager) {
    var tagger = $(this)
    tagger.addClass("tagger")
    tagger.attr({
        "tabindex": "-1"
    })
    var input = $("<input id='" + id + "' type='text' placeholder='" + placeholder + "' class='ac'>")
    tagger.html(input)

    input.blur(function() {
        tagger.css({
            "background": "#fcfcfc"
        })
    })

    this.keyup(function(e) {
        if (e.keyCode == 13) {
            if ((input.val().trim()).length > 0) {
                $(".autosuggest").remove()
                addAsTag(input, placeholder, tagger, null)
            }
        } else {
            var tag = input.val().trim()
            input.autoSuggest("interest", tag, tagger.width(), placeholder, tagger)
            if (tag.length == 3) {
                if ($.inArray(tag, tag_key) == -1) {
                    tag_key.push(tag)
                    $.ajax({
                        url: "manager/" + tagManager + ".php",
                        cache: false,
                        type: "get",
                        dataType: "json",
                        data: "req=get_tags_by_key&key=" + tag,
                        beforeSend: function() {

                        },
                        success: function(data) {
                            var i, data_length = data.length
                            var cur_size = tag_list.length
                            for (i = 0; i < data_length; i++) {
                                tag_list[i + cur_size] = new Tags()
                                tag_list[i + cur_size].id = data[i].id
                                tag_list[i + cur_size].name = data[i].name
                                tag_list[i + cur_size].popularity = data[i].popularity
                            }
                        },
                        error: function(e, f) {

                        }
                    })
                }
            }
        }
    })

    this.click(function() {
        $(this).find("input").focus()
        $(this).css({
            "background": "white"
        })
    })
    this.hover(function() {
        input.css("background", "white")
        $(this).css({
            "background": "white"
        })
    }, function() {
        input.css("background", "#fcfcfc")
        $(this).css({
            "background": "#fcfcfc"
        })
    })
}

$.fn.fitImage = function(img, photo_width, photo_height, flex) {
    var image_container = $(this)
    var w, h, mgn_top, mgn_left
    if (flex == "both") {

        var canvasWidth = parseInt(image_container.width());
        var canvasHeight = parseInt(image_container.height());

        var minRatio = Math.min(canvasWidth / photo_width, canvasHeight / photo_height);
        w = minRatio * photo_width;
        h = minRatio * photo_height;

        mgn_left = (canvasWidth - w) / 2;
        mgn_top = (canvasHeight - h) / 2;

        img.width(w)
        img.height(h)
        img.css({
            "margin-left": mgn_left,
            "margin-top": mgn_top
        })

//
//
//
//
//        if (photo_width < image_container.width() && photo_height < image_container.height()) {
//            w = photo_width
//            h = photo_height
//            mgn_top = (image_container.height() - h) / 2
//            mgn_left = (image_container.width() - w) / 2
//            img.width(w)
//            img.height(h)
//            img.css({
//                "margin-left": mgn_left,
//                "margin-top": mgn_top
//            })
//        } else if (photo_width > image_container.width() && photo_height < image_container.height()) {
//            w = image_container.width() - 5
//            h = (photo_height / photo_width) * w
//            mgn_top = (image_container.height() - h) / 2
//            img.width(w)
//            img.height(h)
//            img.css({
//                "margin-top": mgn_top
//            })
//        } else if (photo_width < image_container.width() && photo_height > image_container.height()) {
//            h = image_container.height() - 5
//            w = (photo_width / photo_height) * h
//            mgn_left = (image_container.width() - w) / 2
//            img.height(h)
//            img.width(w)
//            img.css({
//                "margin-left": mgn_left
//            })
//        } else {
//            if (photo_width > photo_height) {
//                w = image_container.width() - 5
//                h = w * (photo_height / photo_width)
//                mgn_top = (image_container.height() - h) / 2
//                img.height(h)
//                img.width(w)
//                img.css({
//                    "margin-top": mgn_top
//                })
//            } else {
//                h = image_container.height() - 5
//                w = h * (photo_width / photo_height)
//                mgn_left = (image_container.width() - w) / 2
//                img.height(h)
//                img.width(w)
//                img.css({
//                    "margin-left": mgn_left
//                })
//            }
//        }
    }
    var width_input = $("<input type='hidden' value='" + photo_width + "' class='wi'>")
    var height_input = $("<input type='hidden' value='" + photo_height + "' class='hi'>")
    var width_output = $("<input type='hidden' value='" + w + "' class='wo'>")
    var height_output = $("<input type='hidden' value='" + h + "' class='ho'>")
    image_container.html(img)
    image_container.append(width_input)
    image_container.append(height_input)
    image_container.append(width_output)
    image_container.append(height_output)
}

$.fn.dropDownCheckList = function(options) {
    var settings = $.extend({
        source: null,
        max_height: "0px",
        placeholder: null,
        button: null,
        buttonFunction: null,
        multiple: false,
        callback: function() {
        }
    }, options);
    var context = this
    this.addClass("dropDownCheckList")
    this.html("<div style='color:#777;padding-left:5px;padding-top:3px;'>" + settings.placeholder + "<div class='ddtriangle'></div></div>")
    this.click(function(e) {
        e.stopPropagation()
        var menu = $("<div onclick='var event = arguments[0] || window.event; event.stopPropagation()' style='overflow-y:scroll;background:white;position:absolute;z-index:1;padding:5px;' class='dropDownCheckListMenu uni_shadow_light'>"), i
        menu.height(settings.max_height)
        menu.width(context.width())
        var checked_list = [], i = 0
        context.find(".tag").each(function() {
            checked_list[i] = $.trim(($(this).children("input[type='hidden']").val()))
            i++
        })
        if (settings.button != null) {
            var button = $("<div id='cs_button' class='bbutton'>")
            button.html(settings.button)
            menu.append(button)

            button.click(function() {
                window[settings.buttonFunction]($(this))
            })
        }
        for (i = 0; i < settings.source.length; i++) {
            var item = $("<div class='option'>")
            if (settings.multiple == true) {
                if (checked_list.indexOf($.trim(settings.source[i].id)) != -1)
                    item.html("<input type='checkbox' id='ddcl" + settings.source[i].id + "' checked>")
                else
                    item.html("<input type='checkbox' id='ddcl" + settings.source[i].id + "'>")
            } else {
                if (checked_list.indexOf($.trim(settings.source[i].id)) != -1)
                    item.html("<input type='radio' id='ddcl" + settings.source[i].id + "' checked>")
                else
                    item.html("<input type='radio' id='ddcl" + settings.source[i].id + "'>")
            }

            item.append("<label for='ddcl" + settings.source[i].id + "'>" + settings.source[i].name + "</label>")
            menu.append(item)
        }
        context.html(menu)
    })
    $("html").click(function() {
        if ($(".dropDownCheckListMenu").length > 0) {
            var menu = $(".dropDownCheckListMenu"), cnt = 0
            menu.children(".option").each(function() {
                if ($(this).children("input").is(":checked")) {
                    cnt++
                    var hi = $("<input type='hidden' value='" + $(this).children("input").attr("id").substr(4) + "'>")
                    var name = $(this).find("label").html()
                    var tagdiv = $("<div class='tag' style=''>")
                    tagdiv.html("<span class='val'>" + name + "</span>")
                    tagdiv.append(hi)
                    context.append(tagdiv)
                }
            })
            if (cnt == 0) {
                context.html("<div style='color:#777;padding-left:5px;padding-top:3px;'>" + settings.placeholder + "<div class='ddtriangle'></div></div>")
            } else {
                context.css({
                    "background": "#fcfcfc"
                })
            }
            menu.remove()
        }
    })
}

$.fn.ratingWidget = function(options) {
    var context = this, i;
    var settings = $.extend({
        max: 5,
        theme: "gold",
        fixed: false
    }, options)
    var rating = context.children("input[type='hidden']").val()
    var id = context.attr("id").substring(6)
    if (settings.fixed == false) {
        for (i = settings.max; i > 0; i--) {
            var radio = $("<input type='radio' class='rating-input' id='rating-input-" + i + "' name='rating-input-" + i + "'>")
            var label = $("<label for='rating-input-" + i + "' class='rating-star'>")
            context.append(radio)
            context.append(label)
        }
    } else {
        var label = []
        for (i = settings.max; i > 0; i--) {
            label[i] = $("<label class='fixed-rating-star' id='r" + i + "_" + id + "'>")
            context.append(label[i])
            if (rating > 0) {
                $("#r" + i + "_" + id).css("background-position", "0 0")
                rating--
            }
        }
    }
}

$.fn.addRatingWidget = function(options) {

    var context = this, i, star = []
    var settings = $.extend({
        total: 5,
        rating: 0,
        border_color: "#ccc",
        inactive_color: "white",
        active_color: "gold"
    }, options);
    var rating = Math.round(settings.rating);
    if ($(context).attr("data-rating") != null && $(context).attr("data-rating") != "") {
        rating = Math.round($(context).attr("data-rating"));
    }
    for (i = settings.total - 1; i > -1; i--) {
        star[i] = $("<div class='star fl'>")
        if (i < rating) {
            star[i].removeClass("star")
            star[i].addClass("star_gold")
        }
        context.prepend(star[i])
    }
}

$.fn.Like = function(id, type, like_id, element, theme) {
    var span = $("<span class='post_op'>"), input
    if (like_id == 0) {
        span.html("Like")
        input = $("<input type='hidden' value='Like'>")
    } else {
        span.html("Unlike")
        span.append("<input type='hidden' value='" + like_id + "' class='like_id'>")
        input = $("<input type='hidden' value='Unlike'>")
    }
    span.append(input)
    if (theme == "dark") {
        span.removeClass("post_op")
        span.addClass("post_op_dark")
    }
    $(this).append(span)
    span.click(function() {
        if (input.val() == "Like") {
            $.ajax({
                url: "manager/PostManager.php",
                type: "get",
                cache: false,
                data: "req=like&type=" + type + "&id=" + id + "&user_id=" + user_id,
                beforeSend: function() {
                    input.val("Unlike")
                    span.html("Unlike")
                    span.append(input)
                },
                success: function(like_id) {
                    span.append("<input type='hidden' value='" + like_id + "' class='like_id'>")
                },
                error: function(e, f) {
                    input.val("Like")
                    span.html("Like")
                    span.append(input)
                    alertBox("Some problem occured. Please try again later.")
                }
            })
        } else if (input.val() == "Unlike") {
            var like_id = span.children("input.like_id").val()
            $.ajax({
                url: "manager/PostManager.php",
                type: "get",
                cache: false,
                data: "req=unlike&type=" + type + "&post_id=" + id + "&like_id=" + like_id,
                beforeSend: function() {
                    input.val("Like")
                    span.html("Like")
                    span.append(input)
                },
                error: function(e, f) {
                    input.val("Unlike")
                    span.html("Unlike")
                    span.append(input)
                    alertBox("Some problem occured. Please try again later.")
                }
            })
        }
    })
}

$.fn.Share = function(id, post_type, element_info, theme, callback) {
    var span;
    if (callback == null)
        span = $("<span class='post_op'>");
    else
        span = $("<span class='post_op' data-callback='" + callback + "'>");
    span.html("Share");
    if (theme == "dark") {
        span.removeClass("post_op");
        span.addClass("post_op_dark");
    }
    $(this).append(span);
    span.click(function() {
        var callbackname = null;
        if ($(this).attr("data-callback")) {
            callbackname = $(this).attr("data-callback");
        }
        if (user_sets.length == 0) {
            var cvr;
            $.ajax({
                url: "manager/SetsManager.php",
                type: "get",
                cache: true,
                data: "req=get_sets&get_preview=0&user_id=" + user_id,
                dataType: "json",
                beforeSend: function() {
                    cvr = $("<div class='white_overlay'>")
                    cvr.html("<center><img src='img/massive_ajax_loader.gif' style='margin-top:30px;'></center>")
                    $("body").prepend(cvr)
                },
                success: function(data) {
                    user_sets = data;
                    cvr.remove();
                    prepareShareBox(id, post_type, element_info, callbackname);
                }, error: function(e, f) {
                    cvr.remove()
                    alertBox()
                }
            })
        } else {
            prepareShareBox(id, post_type, element_info, callbackname);
        }
    })
}

function prepareShareBox(id, post_type, element_info, callbackname) {
    var shareBox = new Box("sharebox", "50", "56")
    shareBox.heading = "Share post"
    shareBox.createOverlay(1)
    var main_body = shareBox.createBox()

    var foreign_share_div = $("<div id='foreign_share_div' style='width:100%;height:55px;background:#fcfcfc;border-bottom:1px solid #ccc;'>")
    main_body.html(foreign_share_div)
    var post_element_div = $("<div id='post_element_div' style='float:left;margin-top:10px;margin-left:10px;'>")
    main_body.append(post_element_div);
    post_element_div.width(0.35 * main_body.width())
    post_element_div.height(post_element_div.width())
    if (post_type == "photo" || post_type == "link") {
        var image_div = $("<div id='image_div' style='height:100%;width:100%;display:table;' data-parent-id='" + id + "'>")
        post_element_div.html(image_div)
        image_div.fitImage(element_info[0], element_info[1], element_info[2], "both")
        element_info[0].addClass("uni_shadow_dark")
        element_info[0].css("border", "5px solid white")
    } else if (post_type == "video") {
        var image_div = $("<div style='height:100%;width:100%;display:table;'>")
        post_element_div.html(image_div)
        image_div.fitImage(element_info[0], element_info[1], element_info[2], "both")
        element_info[0].addClass("uni_shadow_dark")
        element_info[0].css("border", "5px solid white")
    }

    var form_cover = $("<div id='form_cover' style='float:left;margin-left:15px;overflow-y:scroll;overflow-x:hidden;'>")
    main_body.append(form_cover)
    form_cover.width(0.61 * main_body.width())
    form_cover.height(main_body.height() - 56)
    var form_div = $("<div id='form_div'>")
    form_cover.append(form_div)
    form_div.width(form_cover.width() - 20)

    var user_comment_div = $("<div style='display:table;width:100%'>")
    form_div.html(user_comment_div)
    var user_pic_div = $("<div style='display:table;float:left;width:40px;height:40px;border-radius:3px;margin-top:10px;'>")
    user_comment_div.append(user_pic_div)
    var user_pic = blur_profile_pic.clone()
    user_pic.width(40)
    user_pic.height(40)
    user_pic.css("border-radius", "3px")
    user_pic_div.html(user_pic)
    var share_text = $("<textarea id='share_text' placeholder='Share your thoughts' class='fl'>")
    user_comment_div.append(share_text)

    var set_input_table = $("<table style='width:100%;width:calc(100% - 0.3125em)'>")
    var sit_tr = $("<tr>")
    set_input_table.html(sit_tr)
    var create_set_td = $("<td>")
    var create_set = $("<input type='button' class='bbutton' value='Create set' style='margin-left:50px;margin-top:10px;float:left;width:100px;clear:both;'>")
    create_set_td.html(create_set)
    create_set.click(function() {
        callSetCreator($("#sharebox"), $(this))
    })
    create_set_td.append("&nbsp;<div style='vertical-align:bottom;float:left;margin-top:0.8em;margin-left:10px;'><b>OR</b></div>&nbsp;")
    var set_input_td = $("<td style='width:50%;' id='sinp'>")
    var set_input = $("<select class='dropdown' id='post_set' style='float:left;'>")
    var i;
    set_input.html("<option value='-1'>Choose set</option>")
    for (i = 0; i < user_sets.length; i++) {
        set_input.append("<option value='" + user_sets[i].id + "'>" + user_sets[i].name + "</option>");
    }
    set_input_td.html(set_input);
    sit_tr.html(create_set_td);
    sit_tr.append(set_input_td);
    set_input.easyDropDown({cutOff: 5});
    form_div.append(set_input_table);

    var interest_tagger = $("<div id='interest_tagger' style='margin-left:50px'>")
    form_div.append(interest_tagger)
    interest_tagger.width(form_div.width() - 60)
    interest_tagger.addTagger("share_interest_tag", "Tag interests", "TagManager")

    var share_settings = $("<div style='width:100%;margin-top:5px;margin-left:50px'>")
    form_div.append(share_settings)

    var sharable = $("<input type='checkbox' id='sharable' class='fl'  name='sharable' checked>")
    share_settings.append("<label for='sharable' class='fl'>Sharable</label>")
    share_settings.append(sharable)

    var commentable = $("<input type='checkbox' id='commentable' class='fl' name='commentable' checked>")
    share_settings.append("<label for='commentable' class='fl' style='margin-left:5px;'>Allow comments</label>")
    share_settings.append(commentable)

    share_settings.append("<br>")

    var share_button = $("<input type='button' class='bbutton fl' value='Share' style='width:80px;margin-left:50px;margin-top:10px;margin-bottom:10px;'>")
    var cancel_button = $("<input type='button' class='wbutton fl' value='Cancel' style='margin-left:5px;width:80px;margin-top:10px;margin-bottom:10px;'>")

    form_div.append(share_button)
    form_div.append(cancel_button)

    cancel_button.click(function() {
        shareBox.closeBox()
    })
    share_button.click(function() {
        var context = $(this)
        var sets = $("#post_set").val(), i = 0, tags = []

        if (sets == -1) {
            $("#post_set").css({
                "border-color": "red",
                "background": "#eed3d7"
            })
            return
        } else {
            i = 0;
            $("#interest_tagger").find(".tag").each(function() {
                var t = $(this)
                tags[i] = [t.children("input[type='hidden']").val(), $.trim(t.children(".val").html())]
                i++
            })
            var is_sharable = 1, is_commentable = 1;
            if ($("#sharable").is(":checked") == false)
                is_sharable = 0
            if ($("#commentable").is(":checked") == false)
                is_commentable = 0
            var postObject = {id: $("#image_div").attr("data-parent-id"), sharer_id: user_id, share_text: $("#share_text").val(), set: sets, tag: tags, sharable: is_sharable, commentable: is_commentable}
            $.ajax({
                url: "manager/PostManager.php",
                type: "get",
                data: "req=share_post&post=" + encodeURIComponent(JSON.stringify(postObject)),
                beforeSend: function() {
                    context.hide()
                    context.replaceWith("<img src='img/ajax_loader_horizontal.gif' id='loader'>")
                }, success: function(share_id) {
                    shareBox.closeBox();
                    if (callbackname != null) {
                        window[callbackname]();
                    } else {
                        alert("asd")
                    }
                }, error: function(e, f) {
                    $("#loader").remove()
                    context.show()
                    alertBox()
                }
            })
        }
    })
}

$.fn.Comment = function(id, theme) {
    var span = $("<span class='post_op' id='c" + id + "'>")
    span.html("Comment")
    if (theme == "dark") {
        span.removeClass("post_op")
        span.addClass("post_op_dark")
    }
    $(this).append(span)
    $(this).addClass("comment_span")
    var comment_div;
    var comment_text;
    $("html").click(function() {
        $(".comment_div").remove()
    })
    span.click(function(e) {
        e.stopPropagation()
        if ($(".comment_div").length == 0) {
            comment_div = $("<div class='comment_div' style='background:white;position:absolute;z-index:10;border:1px solid #ccc;border-top:0;' onclick='var event = arguments[0] || window.event; event.stopPropagation()'>")
            comment_text = $("<textarea class='comment_text' style='padding-top:5px;height:28px;' onclick='var event = arguments[0] || window.event; event.stopPropagation()'>")
            comment_div.html(comment_text)
            comment_div.width($(this).parent().parent().width() - 1)
            comment_text.width(comment_div.width() - 10)
            $(this).parent().parent().append(comment_div)
            comment_text.focus()
            comment_text.elastic()
            comment_div.css("border", "1px solid #007dff")
            comment_text.focus(function() {
                comment_div.css("border", "1px solid #007dff")
            })
            comment_text.on("keyup", null, function(e) {
                if (e.keyCode == 13) {
                    var ta = $(this)
                    var post_id = ta.parent().parent().attr("id").substr(5)
                    var comment = $.trim(ta.val())
                    $.ajax({
                        url: "manager/PostManager.php",
                        type: "get",
                        data: "req=post_comment&post_id=" + post_id + "&comment=" + comment + "&type=text&user_id=" + user_id,
                        beforeSend: function() {
                            ta.hide()
                            ta.parent().append("<center><img src='img/ajax_loader_horizontal.gif' style='width:20px;'></center>")
                            ta.css("border", "1px solid #ccc")
                        },
                        success: function(r) {
                            if (r == -1) {
                                center.parent().remove()
                            } else {
                                var center = $("<center>")
                                center.html("Comment posted")
                                ta.parent().html(center)
                                center.parent().fadeOut("1500", function() {
                                    center.parent().remove()
                                })
                            }
                        }, error: function(e) {
                            alertBox()
                        }
                    })
                }
            })
        } else {
            $(".comment_div").remove()
        }
    })
}

$.fn.menuButton = function(options) {
    //establish the defaults
    var settings = $.extend({
        source: ["Option1", "Option2", "Option3"],
        sourceImage: ["img/friendreq.png", "img/friendreq.png", "img/friendreq.png"],
        width: "80px",
        callback: function() {
        }
    }, options);

    var context = this
    this.addClass("menuButton")
    this.css("width", settings.width)
    this.html("<img src='" + settings.sourceImage[0] + "' style='width:22px;margin-right:5px;'><span style='margin-bottom:5px;'>" + settings.source[0] + "</span>")
    this.click(function(e) {
        var i
        var menudiv = $("<div>")
        menudiv.addClass("menuButtonMenu")
        menudiv.css("width", settings.width)
        for (i = 1; i < settings.source.length; i++) {
            var div = $("<div id='mbop" + i + "'>")
            div.html("<img src='" + settings.sourceImage[i] + "' style='width:22px;margin-right:5px;'><span style='margin-bottom:5px;'>" + settings.source[i] + "</span>")
            div.addClass("menuButtonOptions")
            menudiv.append(div)
            div.click(function(e) {
                var index = $(this).attr("id").substr(4)
                var temp = settings.source[0]
                settings.source[0] = settings.source[index]
                settings.source[index] = temp

                temp = settings.sourceImage[0]
                settings.sourceImage[0] = settings.sourceImage[index]
                settings.sourceImage[index] = temp

                context.html("<img src='" + settings.sourceImage[0] + "' style='width:22px;margin-right:5px;'><span style='margin-bottom:5px;'>" + settings.source[0] + "</span>")
                menudiv.remove()
                settings.callback.call(context, settings.source[0])
                e.stopPropagation()
            })
        }
        context.append(menudiv)
        $("html").click(function() {
            $(".menuButtonMenu").remove()
            e.stopPropagation()
        })
        e.stopPropagation()
    })
    return settings.source[0]
}

$.fn.smartSearch = function(options) {
    //establish the defaults
    var buffer = [];
    var sbuffer = [];
    var keybuffer = [];
    var context = this;
    var countdown = 0;
    var settings = $.extend({
        listSuggestion: true,
        listThumbnails: false,
        dataSourceURL: "",
        dataSourceReturnType: "json",
        dataSourceParameters: "",
        textParameter: "text",
        autoComplete: true,
        approach: "min_char",
        min_char: 3,
        time_interval: 500,
        callback: function() {
        }
    }, options || {});

    this.keyup(function(e) {
        if (e.keyCode == 27) {
            $("#searchview").remove();
            return;
        }
        if (!((e.keyCode >= 48 && e.keyCode <= 90) || e.keyCode == 32 || e.keyCode == 8 || e.keyCode == 13 || (e.keyCode >= 96 && e.keyCode <= 111) || (e.keyCode >= 186 && e.keyCode <= 192 || (e.keyCode >= 219 && e.keyCode <= 222)))) {
            return;
        }
        var text = $.trim(context.val());
        if (settings.approach == "min_char") {
            if (e.keyCode != 13 && settings.listSuggestion == true) {
                if (text.length == settings.min_char) {
                    if ($.inArray(text, keybuffer) != -1) {
                        if (settings.autoComplete == true) {
                            context.autocomplete({
                                source: buffer
                            });
                        } else {
                            settings.callback.call(context, buffer)
                        }
                    } else {
                        $.ajax({
                            url: settings.dataSourceURL,
                            dataType: settings.dataSourceReturnType,
                            type: "get",
                            data: settings.dataSourceParameters + "&sugg=1&" + settings.textParameter + "=" + encodeURIComponent(text),
                            success: function(data) {
                                keybuffer[keybuffer.length] = text;
                                var i, buffer_length = buffer.length;
                                for (i = buffer_length; i < buffer_length + data.length; i++) {
                                    buffer[i] = data[i - buffer_length];
                                }
                                if (settings.autoComplete == true) {
                                    context.autocomplete({
                                        source: buffer
                                    })
                                } else {
                                    settings.callback.call(context, buffer)
                                }
                            }, error: function(e, f) {
                                alertBox()
                            }
                        })
                    }
                } else {
                    var dtext = text.substr(0, settings.min_char)
                    if ($.inArray(dtext, keybuffer) != -1) {
                        if (settings.autoComplete == true) {
                            context.autocomplete({
                                source: buffer
                            })
                        } else {
                            settings.callback.call(context, buffer)
                        }
                    }
                }
            } else {
                if (text in sbuffer) {
                    settings.callback.call(context, sbuffer[text])
                } else {
                    $.ajax({
                        url: settings.dataSourceURL,
                        dataType: settings.dataSourceReturnType,
                        type: "get",
                        data: settings.dataSourceParameters + "&sugg=0&" + settings.textParameter + "=" + encodeURIComponent(text),
                        success: function(data) {
                            sbuffer[text] = []
                            sbuffer[text][0] = data
                            settings.callback.call(context, data)
                        }, error: function(e, f) {
                            alertBox()
                        }
                    })
                }
            }
        } else if (settings.approach == "time_interval") {
            if (countdown)
                clearTimeout(countdown);
            countdown = setTimeout(function() {
                sendSearchRequest(text)
            }, settings.time_interval);
        }
    })

    this.keydown(function() {
        clearTimeout(countdown);
    });

    function sendSearchRequest(text) {
        if (text in sbuffer) {
            settings.callback.call(context, sbuffer[text])
        } else {
            $.ajax({
                url: settings.dataSourceURL,
                dataType: settings.dataSourceReturnType,
                type: "get",
                data: settings.dataSourceParameters + "&" + settings.textParameter + "=" + encodeURIComponent(text),
                success: function(data) {
                    sbuffer[text] = data
                    settings.callback.call(context, data);
                }, error: function(e, f) {
                    alertBox()
                }
            })
        }
    }
}

//Alertbox
function alertBox(msg) {
    var outputString = "<div id='alertBoxFade' class='white_overlay'></div>";
    outputString += "<div id='alertBox'>";
    outputString += "<div id='alertBox_header'class='light_header'><label>Message</label></div>";
    if (msg == null || msg.length == 0)
        msg = "Some error occured. Please try again later."
    outputString += "<div class='msg'>" + msg + "</div>";
    outputString += "<div id='alertBox_buttons'><center><input type='button' value='OK' class='bbutton' id='alertBox_ok' style='width:70px;' onclick='alertBox_close()'></center></div></div>";
    $("body").append(outputString);

    $("#alertBox").css("left", 0.3 * window_width);
    $("#alertBox").css("width", 0.4 * window_width);

    var height = $("#alertBox").height();
    height = height / window_height;
    var top = (1 - height) / 2;
    $("#alertBox").css("top", top * window_height);
}

function alertBox_close() {
    $("body").children("#alertBox").remove();
    $("#alertBoxFade").remove();
}

//Box constructs
function Box(box_id, width, height) {
    var overlay = [box_id + "_overlay", "white"]
    var loading = true
    var loading_image = "img/big_ajax_loader.gif"
    var loading_text
    var horizontal_position = "center"
    var vertical_position = "center"
    var entry_animation = ["appear", "", 0]
    var exit_animation = ["appear", "", 0]
    var no_head = false
    var heading
    var buttons = []
    var onclose = null
    var onCloseArg = null
    var context = this

    this.createOverlay = function(removeOnClick) {
        var overlay_element, overlay_id, overlay_type
        if (this.overlay == undefined) {
            overlay_id = overlay[0]
            overlay_type = overlay[1]
        } else {
            overlay_id = this.overlay[0]
            overlay_type = this.overlay[1]
        }
        if (overlay_type == "transparent")
            overlay_element = $("<div>").addClass("t_overlay")
        else
            overlay_element = $("<div>").addClass("white_overlay")
        overlay_element.attr("id", overlay_id)
        $("body").append(overlay_element)
        overlay_element.click(function() {
            if (removeOnClick == 1) {
                context.closeBox()
            }
        })
    }

    this.createBox = function() {
        var margin_left = (1 - width / 100) * (screen.width) / 2
        var margin_top = (1 - height / 100) * (screen.height) / 2
        var entry_animation_effect, entry_animation_direction, entry_animation_duration, exit_animation_effect, exit_animation_direction, exit_animation_duration
        if (this.entry_animation == undefined) {
            entry_animation_effect = entry_animation[0]
            entry_animation_direction = entry_animation[1]
            entry_animation_duration = entry_animation[2]
        } else {
            entry_animation_effect = this.entry_animation[0]
            entry_animation_direction = this.entry_animation[1]
            entry_animation_duration = this.entry_animation[2]
        }
        if (this.exit_animation == undefined) {
            exit_animation_effect = exit_animation[0]
            exit_animation_direction = exit_animation[1]
            exit_animation_duration = exit_animation[2]
        } else {
            exit_animation_effect = this.exit_animation[0]
            exit_animation_direction = this.exit_animation[1]
            exit_animation_duration = this.exit_animation[2]
        }

        var box = $("<div>")
        box.attr("id", box_id)
        box.addClass("front_box")
        box.css({
            "width": width * (screen.width) / 100,
            "height": height * (screen.height) / 100
        })

        var close_button, head_label, box_head
        box_head = $("<div>")
        box_head.attr("id", box_id + "_head")
        box_head.addClass("light_header")

        head_label = $("<label>")
        head_label.html(this.heading)

        close_button = $("<label>")
        close_button.html("X")
        close_button.addClass("box_close")
        if (this.no_head == false || this.no_head == null) {
            box.append(box_head)
            box_head.append(head_label)
            box_head.append(close_button)
        }

        var main_body = $("<div>")
        main_body.addClass("box_main_body")
        if (this.no_head == false || this.no_head == null)
            main_body.height(box.height() - 41)
        else
            main_body.height(box.height())
        box.append(main_body)

        if (this.buttons == undefined) {
            var button_div = $("<div>")
            box.append(button_div)
        }

        box.css({
            "margin-left": margin_left,
            "margin-top": margin_top - 50
        })

        if (entry_animation_effect == "appear") {
            $("body").prepend(box)
        } else if (entry_animation_effect == "fly") {
            $("body").prepend(box)
            box.hide()
            if (entry_animation_direction == "up") {
                box.show("slide", {
                    direction: "up"
                }, entry_animation_duration)
            } else if (entry_animation_direction == "down") {
                box.show("slide", {
                    direction: "down"
                }, entry_animation_duration)
            } else if (entry_animation_direction == "left") {
                box.show("slide", {
                    direction: "left"
                }, entry_animation_duration)
            } else if (entry_animation_direction == "right") {
                box.show("slide", {
                    direction: "right"
                }, entry_animation_duration)
            }
        } else if (entry_animation_effect == "book_open") {
            box.addClass("page")
            $("body").prepend(box)
            box.parent().addClass("perspectiveClass")
            if (entry_animation_direction == "up") {
                box.show("slide", {
                    direction: "up"
                }, entry_animation_duration)
            } else if (entry_animation_direction == "down") {
                box.show("slide", {
                    direction: "down"
                }, entry_animation_duration)
            } else if (entry_animation_direction == "left") {
                box.addClass("open_left")
            }
            else if (entry_animation_direction == "right") {
                box.show("slide", {
                    direction: "right"
                }, entry_animation_duration)
            }
        }

        this.closeBox = function() {
            var overlay_id
            if (this.overlay == undefined) {
                overlay_id = overlay[0]
            } else {
                overlay_id = this.overlay[0]
            }
            if (exit_animation_effect == "appear") {
                $("#" + overlay_id).remove()
                $("#" + box_id).remove()
            } else if (exit_animation_effect == "fly") {
                if (exit_animation_direction == "up") {
                    box.hide("slide", {
                        direction: "up"
                    }, entry_animation_duration, function() {
                        $("#" + overlay_id).remove()
                        $("#" + box_id).remove()
                    })
                } else if (exit_animation_direction == "down") {
                    box.hide("slide", {
                        direction: "down"
                    }, entry_animation_duration, function() {
                        $("#" + overlay_id).remove()
                        $("#" + box_id).remove()
                    })
                } else if (exit_animation_direction == "left") {
                    box.hide("slide", {
                        direction: "left"
                    }, entry_animation_duration, function() {
                        $("#" + overlay_id).remove()
                        $("#" + box_id).remove()
                    })
                }
                else if (exit_animation_direction == "right") {
                    box.hide("slide", {
                        direction: "right"
                    }, entry_animation_duration, function() {
                        $("#" + overlay_id).remove()
                        $("#" + box_id).remove()
                    })
                }
            }
        }

        close_button.click(function() {
            context.closeBox()
            if (context.onclose != null) {
                window[context.onclose](context.onCloseArg)
            }
        })
        return main_body
    }
}

function scriptLoader(script_name, function_to_call, params, allow, caller) {
    //params is 2d array...i for funtion j for args
    var script_url = "js/" + script_name + ".js"
    caller = $(caller)
    var caller_html = caller.html()
    var function_array = function_to_call.split(","), i
    if ($.inArray(script_name, loaded_script) == -1) {
        if (caller != null) {
            if (allow == 1)
            {
                var img = $("<img src='img/ajax_loader_horizontal.gif'>")
                caller.html(img)
            } else {
                var div = $("<div>")
                div.addClass("white_overlay")
                div.html("<center><img src='img/massive_ajax_loader.gif'></center>")
                div.find("center").css({
                    "margin-top": "50px"
                })
                $("body").append(div)
            }
        }
        $.getScript(script_url, function() {
            for (i = 0; i < function_array.length; i++)
            {
                window[function_array[i]](params[i])
            }
            if (caller != null) {
                if (allow == 1)
                    caller.html(caller_html)
                else
                    div.remove()
            }
        })
        loaded_script.push(script_name)
    } else {
        for (i = 0; i < function_array.length; i++)
        {
            window[function_array[i]](params[i])
        }
    }
}

function getPost(e, id) {
    var center = $("<center id='post_loader'>")
    $.ajax({
        url: "manager/PostManager.php",
        cache: false,
        type: "GET",
        dataType: "json",
        data: "req=get_post&id=" + id,
        beforeSend: function() {
            var feed_loader = $("<img style='position:absolute;width:50px;margin-top:80px'>")
            feed_loader.attr("src", "img/massive_ajax_loader.gif")
            center.html(feed_loader)
            e.append(center)
        },
        success: function(data) {
            center.remove()
            var post = new PostTile(data)
            post.arrangeTile(e, 4, "prepend", null)
        }
    })
}

function getUpdates() {
    $.ajax({
        url: "manager/UpdateManager.php",
        type: "get",
        dataType: "json",
        data: "req=get_updates&user_id=17&type=all",
        success: function(updates) {
            var i
            for (i = 0; i < updates.length; i++) {
                var ublock = $("<div class='ublock cp' id='" + updates[i].id + "' onclick='window.location.href=\"" + updates[i].url + "\"'>")
                var uimg_div = $("<div class='uimg_div'>")
                var uimg = $("<img src='" + updates[i].img + "'>")
                var udesc_div = $("<div class='udesc'>")
                var udesc = updates[i].description
                uimg_div.html(uimg)
                udesc_div.html(udesc)
                ublock.html(uimg_div)
                ublock.append(udesc_div)
                $("#ulist").prepend(ublock)
                ublock.hover(function() {
                    $(this).css({
                        "background": "#007dff",
                    })
                    $(this).children(".udesc").css("color", "white")
                }, function() {
                    $(this).css({
                        "background": "white",
                    })
                    $(this).children(".udesc").css("color", "#999")
                })
                setTimeout("getUpdates()", 7000)
            }
        }
    })
}

function openCategoryMenu(e) {
    if ($("#category_menu").length == 0) {
        var div = $("<div id='category_menu' class='uni_shadow_dark'>")
        var const_div = $("<div style='position:fixed;margin-top:-20px;background:white;'>");
        div.html(const_div);
        var input = $("<input id='catsearch' type='search' placeholder='Search categories' style='margin:20px 0;'>");
        const_div.html(input);
        var table = $("<table style='width:100%;margin-top:50px'>")
        var i;
        for (i = 0; i < categories.length; i += 2) {
            var tr = $("<tr>");
            var td1 = $("<td>");
            var td2 = $("<td>");
            tr.html(td1);
            tr.append(td2);
            td1.append("<a id='catimg" + categories[i].id + "' href='category.php?id=" + categories[i].id + "'><img src='" + categories[i].image_src + "'></a>");
            td1.append("<a id='catname" + categories[i].id + "' class='black_link catname' href='category.php?id=" + categories[i].id + "'>" + categories[i].name + "</a>")
            if (i + 1 < categories.length) {
                td2.append("<a id='catimg" + categories[i + 1].id + "' href='category.php?id=" + categories[i + 1].id + "'><img src='" + categories[i + 1].image_src + "'></a>");
                td2.append("<a id='catname" + categories[i + 1].id + "' class='black_link catname' href='category.php?id=" + categories[i + 1].id + "'>" + categories[i + 1].name + "</a>")
            }
            table.append(tr);
        }
        div.append(table);
        $(e).append(div);

        const_div.width(div.width());
        input.keyup(function() {
            var term = $.trim($(this).val());
            var term_length = term.length;
            if (term_length != 0) {
                term = term.toLowerCase();
                $("#category_menu").find("a.catname").each(function() {
                    var category_name = $.trim($(this).html()).toLowerCase();
                    if (category_name.substr(0, term_length) != term) {
                        $(this).parent().hide();
                    } else {
                        $(this).parent().show();
                    }
                });
            } else {
                $("#category_menu").find("a").each(function() {
                    $(this).parent().show();
                });
            }
        })
    }
}

function openRequestBox(p) {
    var i
    var reqbox = new Box("reqbox", "35", "70")
    reqbox.heading = "Friend Requests"
    reqbox.createOverlay(1)
    var main_body = reqbox.createBox()

    var type = $("<table style='width:100%;' class='sub_menu'>")
    var tr = $("<tr>")
    var pft = $("<td style='width:50%;border-right:1px solid #ccc;text-align:center;padding-top:10px;padding-bottom:10px;' class='cp black_link'>")
    var sft = $("<td style='width:50%;text-align:center;padding-top:5px;padding-bottom:5px;' class='cp black_link'>")
    pft.html("Pending Friend Requests")
    sft.html("Sent Friend Requests")
    tr.html(pft)
    tr.append(sft)
    type.html(tr)
    main_body.html(type)

    var req_block = $("<div style='width:100%;overflow:auto;overflow-x:hidden;'>")
    main_body.append(req_block)
    req_block.height(main_body.height() - type.height() - 20)

    pft.click(function() {
        if ($(this).hasClass("crt") == false) {
            pft.addClass("black_link_active")
            sft.removeClass("black_link_active")
            sft.removeClass("crt")
            req_block.html("")
            enlist("pending", req_block)
        }
    })

    sft.click(function() {
        if ($(this).hasClass("crt") == false) {
            sft.addClass("black_link_active")
            pft.removeClass("black_link_active")
            pft.removeClass("crt")
            req_block.html("")
            enlist("sent", req_block)
        }
    })

    if (p == "pending") {
        pft.addClass("black_link_active")
        pft.addClass("crt")
    } else if (p == "sent") {
        sft.addClass("crt")
        sft.addClass("black_link_active")
    }

    enlist(p, req_block)
}

function enlist(p, req_block) {
    if (p == "pending" && pfr == 0) {
        req_block.html("<span style='font-size:18px;color:#555;margin:20px;'>No pending requests for you</span>")
        return
    } else if (p == "sent" && sfr == 0) {
        req_block.html("<span style='font-size:18px;color:#555;margin:20px;'>You haven't sent any requests</span>")
        return
    }
    var i, freq_length = freq.length
    for (i = 0; i < freq_length; i++) {
        if (p == freq[i].req_type) {
            var block = $("<div class='med_list_hoverless'>")
            var img = $("<img class='rounded' style='width:40px;height:40px;margin-left:1px;'>")
            img.attr("src", freq[i].profile_pic)
            block.html(img)
            var name = $("<a class='ml1' style='vertical-align:top' href='profile.php?id=" + freq[i].uid + "'>")
            name.html(freq[i].name)
            block.append(name)
            if (p == "pending") {
                var accept = $("<input type='button' class='gbutton fr' value='Accept' style='width:80px;margin-right:10px;' frid='" + freq[i].id + "'>")
                var reject = $("<input type='button' class='wbutton fr' value='Reject' style='width:80px;margin-right:40px;' frid='" + freq[i].id + "'>")
                block.append(reject)
                block.append(accept)
                accept.click(function() {
                    decideReq("accept", $(this).attr("frid"), "reqBox", $(this).parent())
                })
                reject.click(function() {
                    decideReq("reject", $(this).attr("frid"), "reqBox", $(this).parent())
                })
            } else if (p == "sent") {
                var cancel = $("<input type='button' class='wbutton fr' value='Cancel' style='width:80px;margin-right:40px;' frid='" + freq[i].id + "'>")
                block.append(cancel)
                cancel.click(function() {
                    cancelReq($(this).attr("frid"), "reqBox", $(this).parent())
                })
            }
            req_block.append(block)
        }
    }
}

function cancelReq(id, fr, block) {
    var loading = $("<img src='img/ajax_loader_horizontal.gif' class='fr mt1' style='margin-right:120px;'>")
    $.ajax({
        url: "manager/FriendManager.php",
        type: "get",
        cache: false,
        data: "req=cancel&id=" + id,
        beforeSend: function() {
            if (fr == "reqBox") {
                block.children("input").hide()
                block.append(loading)
            }
        },
        success: function(flag) {
            if (fr == "reqBox") {
                if (flag == 1 || flag == 2) {
                    block.hide("slide", {
                        direction: "up"
                    }, 300, function() {
                        block.remove()
                    })
                    var i, k, cnt = 0
                    for (i = 0; i < freq.length; i++) {
                        if (freq[i].id == id) {
                            k = i
                            break
                        }
                        if (freq[i].req_type == "sent") {
                            cnt++
                        }
                    }
                    freq.splice(k, 1)
                    if (cnt == 0) {
                        block.parent().html("<span style='font-size:18px;color:#555;margin:20px;'>You haven't sent any requests</span>")
                        sfr = 0
                    }
                } else {
                    loading.remove()
                    block.children("input").show()
                }
            }
        },
        error: function(e, f) {
            alertBox()
            loading.remove()
            block.children("input").show()
        }
    })
}

function decideReq(decision, id, fr, block) {
    var loading = $("<img src='img/ajax_loader_horizontal.gif' class='fr mt1' style='margin-right:120px;'>")
    $.ajax({
        url: "manager/FriendManager.php",
        type: "get",
        cache: false,
        data: "req=" + decision + "&id=" + id,
        beforeSend: function() {
            if (fr == "reqBox") {
                block.children("input").hide()
                block.append(loading)
            }
        },
        success: function(flag) {
            if (fr == "reqBox") {
                if (flag == 1 || flag == 2) {
                    block.hide("slide", {
                        direction: "up"
                    }, 300)
                    var i, k, cnt = 0
                    for (i = 0; i < freq.length; i++) {
                        if (freq[i].id == id) {
                            k = i
                            break
                        }
                        if (freq[i].req_type == "pending") {
                            cnt++
                        }
                    }
                    freq.splice(k, 1)
                    if (cnt == 0) {
                        req_block.html("<span style='font-size:18px;color:#555;margin:20px;'>No pending requests for you</span>")
                        pfr = 0
                    }
                } else {
                    loading.remove()
                    block.children("input").show()
                }
            }
        },
        error: function(e, f) {
            alertBox()
            loading.remove()
            block.children("input").show()
        }
    })
}


function callSetCreator(parent, e) {
    parent.hide("slide", {direction: "left"}, 250, function() {
        scriptLoader('setCreator', 'setCreator', [[parent]], 1, e)
    })
}

//placeholder UI
(function(b) {
    function d(a) {
        this.input = a;
        a.attr("type") == "password" && this.handlePassword();
        b(a[0].form).submit(function() {
            if (a.hasClass("placeholder") && a[0].value == a.attr("placeholder"))
                a[0].value = ""
        })
    }
    d.prototype = {
        show: function(a) {
            if (this.input[0].value === "" || a && this.valueIsPlaceholder()) {
                if (this.isPassword)
                    try {
                        this.input[0].setAttribute("type", "text")
                    } catch (b) {
                        this.input.before(this.fakePassword.show()).hide()
                    }
                this.input.addClass("placeholder");
                this.input[0].value = this.input.attr("placeholder")
            }
        },
        hide: function() {
            if (this.valueIsPlaceholder() && this.input.hasClass("placeholder") && (this.input.removeClass("placeholder"), this.input[0].value = "", this.isPassword)) {
                try {
                    this.input[0].setAttribute("type", "password")
                } catch (a) {
                }
                this.input.show();
                this.input[0].focus()
            }
        },
        valueIsPlaceholder: function() {
            return this.input[0].value == this.input.attr("placeholder")
        },
        handlePassword: function() {
            var a = this.input;
            a.attr("realType", "password");
            this.isPassword = !0;
            if (b.browser.msie && a[0].outerHTML) {
                var c = b(a[0].outerHTML.replace(/type=(['"])?password\1/gi,
                        "type=$1text$1"));
                this.fakePassword = c.val(a.attr("placeholder")).addClass("placeholder").focus(function() {
                    a.trigger("focus");
                    b(this).hide()
                });
                b(a[0].form).submit(function() {
                    c.remove();
                    a.show()
                })
            }
        }
    };

    var e = !!("placeholder"in document.createElement("input"));
    b.fn.placeholder = function() {
        return e ? this : this.each(function() {
            var a = b(this), c = new d(a);
            c.show(!0);
            a.focus(function() {
                c.hide()
            });
            a.blur(function() {
                c.show(!1)
            });
            b.browser.msie && (b(window).load(function() {
                a.val() && a.removeClass("placeholder");
                c.show(!0)
            }),
                    a.focus(function() {
                        if (this.value == "") {
                            var a = this.createTextRange();
                            a.collapse(!0);
                            a.moveStart("character", 0);
                            a.select()
                        }
                    }))
        })
    }
})(jQuery);

//elastic UI
(function($) {
    jQuery.fn.extend({
        elastic: function() {

            //	We will create a div clone of the textarea
            //	by copying these attributes from the textarea to the div.
            var mimics = [
                'paddingTop',
                'paddingRight',
                'paddingBottom',
                'paddingLeft',
                'fontSize',
                'lineHeight',
                'fontFamily',
                'width',
                'fontWeight',
                'border-top-width',
                'border-right-width',
                'border-bottom-width',
                'border-left-width',
                'borderTopStyle',
                'borderTopColor',
                'borderRightStyle',
                'borderRightColor',
                'borderBottomStyle',
                'borderBottomColor',
                'borderLeftStyle',
                'borderLeftColor'
            ];

            return this.each(function() {

                // Elastic only works on textareas
                if (this.type !== 'textarea') {
                    return false;
                }

                var $textarea = jQuery(this),
                        $twin = jQuery('<div />').css({
                    'position': 'absolute',
                    'display': 'none',
                    'word-wrap': 'break-word',
                    'white-space': 'pre-wrap'
                }),
                lineHeight = parseInt($textarea.css('line-height'), 10) || parseInt($textarea.css('font-size'), '10'),
                        minheight = parseInt($textarea.css('height'), 10) || lineHeight * 3,
                        maxheight = parseInt($textarea.css('max-height'), 10) || Number.MAX_VALUE,
                        goalheight = 0;

                // Opera returns max-height of -1 if not set
                if (maxheight < 0) {
                    maxheight = Number.MAX_VALUE;
                }

                // Append the twin to the DOM
                // We are going to meassure the height of this, not the textarea.
                $twin.appendTo($textarea.parent());

                // Copy the essential styles (mimics) from the textarea to the twin
                var i = mimics.length;
                while (i--) {
                    $twin.css(mimics[i].toString(), $textarea.css(mimics[i].toString()));
                }

                // Updates the width of the twin. (solution for textareas with widths in percent)
                function setTwinWidth() {
                    var curatedWidth = Math.floor(parseInt($textarea.width(), 10));
                    if ($twin.width() !== curatedWidth) {
                        $twin.css({'width': curatedWidth + 'px'});

                        // Update height of textarea
                        update(true);
                    }
                }

                // Sets a given height and overflow state on the textarea
                function setHeightAndOverflow(height, overflow) {

                    var curratedHeight = Math.floor(parseInt(height, 10));
                    if ($textarea.height() !== curratedHeight) {
                        $textarea.css({'height': curratedHeight + 'px', 'overflow': overflow});
                    }
                }

                // This function will update the height of the textarea if necessary 
                function update(forced) {

                    // Get curated content from the textarea.
                    var textareaContent = $textarea.val().replace(/&/g, '&amp;').replace(/ {2}/g, '&nbsp;').replace(/<|>/g, '&gt;').replace(/\n/g, '<br />');

                    // Compare curated content with curated twin.
                    var twinContent = $twin.html().replace(/<br>/ig, '<br />');

                    if (forced || textareaContent + '&nbsp;' !== twinContent) {

                        // Add an extra white space so new rows are added when you are at the end of a row.
                        $twin.html(textareaContent + '&nbsp;');

                        // Change textarea height if twin plus the height of one line differs more than 3 pixel from textarea height
                        if (Math.abs($twin.height() + lineHeight - $textarea.height()) > 3) {

                            var goalheight = $twin.height() + lineHeight;
                            if (goalheight >= maxheight) {
                                setHeightAndOverflow(maxheight, 'auto');
                            } else if (goalheight <= minheight) {
                                setHeightAndOverflow(minheight, 'hidden');
                            } else {
                                setHeightAndOverflow(goalheight, 'hidden');
                            }

                        }

                    }

                }

                // Hide scrollbars
                $textarea.css({'overflow': 'hidden'});

                // Update textarea size on keyup, change, cut and paste
                $textarea.bind('keyup change cut paste', function() {
                    update();
                });

                // Update width of twin if browser or textarea is resized (solution for textareas with widths in percent)
                $(window).bind('resize', setTwinWidth);
                $textarea.bind('resize', setTwinWidth);
                $textarea.bind('update', update);

                // Compact textarea on blur
                $textarea.bind('blur', function() {
                    if ($twin.height() < maxheight) {
                        if ($twin.height() > minheight) {
                            $textarea.height($twin.height());
                        } else {
                            $textarea.height(minheight);
                        }
                    }
                });

                // And this line is to catch the browser paste event
                $textarea.bind('input paste', function(e) {
                    setTimeout(update, 250);
                });

                // Run update once when elastic is initialized
                update();

            });

        }
    });
})(jQuery);

/*! Copyright (c) 2011 Piotr Rochala (http://rocha.la)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version: 1.3.0
 *
 */
(function(f) {
    jQuery.fn.extend({slimScroll: function(h) {
            var a = f.extend({width: "auto", height: "250px", size: "7px", color: "#000", position: "right", distance: "1px", start: "top", opacity: 0.4, alwaysVisible: !1, disableFadeOut: !1, railVisible: !1, railColor: "#333", railOpacity: 0.2, railDraggable: !0, railClass: "slimScrollRail", barClass: "slimScrollBar", wrapperClass: "slimScrollDiv", allowPageScroll: !1, wheelStep: 20, touchScrollStep: 200, borderRadius: "7px", railBorderRadius: "7px"}, h);
            this.each(function() {
                function r(d) {
                    if (s) {
                        d = d ||
                                window.event;
                        var c = 0;
                        d.wheelDelta && (c = -d.wheelDelta / 120);
                        d.detail && (c = d.detail / 3);
                        f(d.target || d.srcTarget || d.srcElement).closest("." + a.wrapperClass).is(b.parent()) && m(c, !0);
                        d.preventDefault && !k && d.preventDefault();
                        k || (d.returnValue = !1)
                    }
                }
                function m(d, f, h) {
                    k = !1;
                    var e = d, g = b.outerHeight() - c.outerHeight();
                    f && (e = parseInt(c.css("top")) + d * parseInt(a.wheelStep) / 100 * c.outerHeight(), e = Math.min(Math.max(e, 0), g), e = 0 < d ? Math.ceil(e) : Math.floor(e), c.css({top: e + "px"}));
                    l = parseInt(c.css("top")) / (b.outerHeight() - c.outerHeight());
                    e = l * (b[0].scrollHeight - b.outerHeight());
                    h && (e = d, d = e / b[0].scrollHeight * b.outerHeight(), d = Math.min(Math.max(d, 0), g), c.css({top: d + "px"}));
                    b.scrollTop(e);
                    b.trigger("slimscrolling", ~~e);
                    v();
                    p()
                }
                function C() {
                    window.addEventListener ? (this.addEventListener("DOMMouseScroll", r, !1), this.addEventListener("mousewheel", r, !1), this.addEventListener("MozMousePixelScroll", r, !1)) : document.attachEvent("onmousewheel", r)
                }
                function w() {
                    u = Math.max(b.outerHeight() / b[0].scrollHeight * b.outerHeight(), D);
                    c.css({height: u + "px"});
                    var a = u == b.outerHeight() ? "none" : "block";
                    c.css({display: a})
                }
                function v() {
                    w();
                    clearTimeout(A);
                    l == ~~l ? (k = a.allowPageScroll, B != l && b.trigger("slimscroll", 0 == ~~l ? "top" : "bottom")) : k = !1;
                    B = l;
                    u >= b.outerHeight() ? k = !0 : (c.stop(!0, !0).fadeIn("fast"), a.railVisible && g.stop(!0, !0).fadeIn("fast"))
                }
                function p() {
                    a.alwaysVisible || (A = setTimeout(function() {
                        a.disableFadeOut && s || (x || y) || (c.fadeOut("slow"), g.fadeOut("slow"))
                    }, 1E3))
                }
                var s, x, y, A, z, u, l, B, D = 30, k = !1, b = f(this);
                if (b.parent().hasClass(a.wrapperClass)) {
                    var n = b.scrollTop(),
                            c = b.parent().find("." + a.barClass), g = b.parent().find("." + a.railClass);
                    w();
                    if (f.isPlainObject(h)) {
                        if ("height"in h && "auto" == h.height) {
                            b.parent().css("height", "auto");
                            b.css("height", "auto");
                            var q = b.parent().parent().height();
                            b.parent().css("height", q);
                            b.css("height", q)
                        }
                        if ("scrollTo"in h)
                            n = parseInt(a.scrollTo);
                        else if ("scrollBy"in h)
                            n += parseInt(a.scrollBy);
                        else if ("destroy"in h) {
                            c.remove();
                            g.remove();
                            b.unwrap();
                            return
                        }
                        m(n, !1, !0)
                    }
                } else {
                    a.height = "auto" == a.height ? b.parent().height() : a.height;
                    n = f("<div></div>").addClass(a.wrapperClass).css({position: "relative",
                        overflow: "hidden", width: a.width, height: a.height});
                    b.css({overflow: "hidden", width: a.width, height: a.height});
                    var g = f("<div></div>").addClass(a.railClass).css({width: a.size, height: "100%", position: "absolute", top: 0, display: a.alwaysVisible && a.railVisible ? "block" : "none", "border-radius": a.railBorderRadius, background: a.railColor, opacity: a.railOpacity, zIndex: 90}), c = f("<div></div>").addClass(a.barClass).css({background: a.color, width: a.size, position: "absolute", top: 0, opacity: a.opacity, display: a.alwaysVisible ?
                                "block" : "none", "border-radius": a.borderRadius, BorderRadius: a.borderRadius, MozBorderRadius: a.borderRadius, WebkitBorderRadius: a.borderRadius, zIndex: 99}), q = "right" == a.position ? {right: a.distance} : {left: a.distance};
                    g.css(q);
                    c.css(q);
                    b.wrap(n);
                    b.parent().append(c);
                    b.parent().append(g);
                    a.railDraggable && c.bind("mousedown", function(a) {
                        var b = f(document);
                        y = !0;
                        t = parseFloat(c.css("top"));
                        pageY = a.pageY;
                        b.bind("mousemove.slimscroll", function(a) {
                            currTop = t + a.pageY - pageY;
                            c.css("top", currTop);
                            m(0, c.position().top, !1)
                        });
                        b.bind("mouseup.slimscroll", function(a) {
                            y = !1;
                            p();
                            b.unbind(".slimscroll")
                        });
                        return!1
                    }).bind("selectstart.slimscroll", function(a) {
                        a.stopPropagation();
                        a.preventDefault();
                        return!1
                    });
                    g.hover(function() {
                        v()
                    }, function() {
                        p()
                    });
                    c.hover(function() {
                        x = !0
                    }, function() {
                        x = !1
                    });
                    b.hover(function() {
                        s = !0;
                        v();
                        p()
                    }, function() {
                        s = !1;
                        p()
                    });
                    b.bind("touchstart", function(a, b) {
                        a.originalEvent.touches.length && (z = a.originalEvent.touches[0].pageY)
                    });
                    b.bind("touchmove", function(b) {
                        k || b.originalEvent.preventDefault();
                        b.originalEvent.touches.length &&
                                (m((z - b.originalEvent.touches[0].pageY) / a.touchScrollStep, !0), z = b.originalEvent.touches[0].pageY)
                    });
                    w();
                    "bottom" === a.start ? (c.css({top: b.outerHeight() - c.outerHeight()}), m(0, !0)) : "top" !== a.start && (m(f(a.start).position().top, null, !0), a.alwaysVisible || c.hide());
                    C()
                }
            });
            return this
        }});
    jQuery.fn.extend({slimscroll: jQuery.fn.slimScroll})
})(jQuery);



/*
 * EASYDROPDOWN - A Drop-down Builder for Styleable Inputs and Menus
 * Version: 2.1.4
 * License: Creative Commons Attribution 3.0 Unported - CC BY 3.0
 * http://creativecommons.org/licenses/by/3.0/
 * This software may be used freely on commercial and non-commercial projects with attribution to the author/copyright holder.
 * Author: Patrick Kunka
 * Copyright 2013 Patrick Kunka, All Rights Reserved
 */

(function(d) {
    function e() {
        this.isField = !0;
        this.keyboardMode = this.hasLabel = this.cutOff = this.disabled = this.inFocus = this.down = !1;
        this.nativeTouch = !0;
        this.wrapperClass = "easydropdown";
        this.onChange = null
    }
    e.prototype = {constructor: e, instances: {}, init: function(a, c) {
            var b = this;
            d.extend(b, c);
            b.$select = d(a);
            b.id = a.id;
            b.options = [];
            b.$options = b.$select.find("option");
            b.isTouch = "ontouchend"in document;
            b.$select.removeClass(b.wrapperClass + " easydropdown");
            b.$select.is(":disabled") && (b.disabled = !0);
            b.$options.length && (b.$options.each(function(a) {
                var c =
                        d(this);
                c.is(":selected") && (b.selected = {index: a, title: c.text()}, b.focusIndex = a);
                c.hasClass("label") && 0 == a ? (b.hasLabel = !0, b.label = c.text(), c.attr("value", "")) : b.options.push({domNode: c[0], title: c.text(), value: c.val(), selected: c.is(":selected")})
            }), b.selected || (b.selected = {index: 0, title: b.$options.eq(0).text()}, b.focusIndex = 0), b.render())
        }, render: function() {
            var a = this;
            a.$container = a.$select.wrap('<div class="' + a.wrapperClass + (a.isTouch && a.nativeTouch ? " touch" : "") + (a.disabled ? " disabled" : "") + '"><span class="old"/></div>').parent().parent();
            a.$active = d('<span class="selected">' + a.selected.title + "</span>").appendTo(a.$container);
            a.$carat = d('<span class="carat"/>').appendTo(a.$container);
            a.$scrollWrapper = d("<div><ul/></div>").appendTo(a.$container);
            a.$dropDown = a.$scrollWrapper.find("ul");
            a.$form = a.$container.closest("form");
            d.each(a.options, function() {
                a.$dropDown.append("<li" + (this.selected ? ' class="active"' : "") + ">" + this.title + "</li>")
            });
            a.$items = a.$dropDown.find("li");
            a.cutOff && a.$items.length > a.cutOff && a.$container.addClass("scrollable");
            a.getMaxHeight();
            a.isTouch && a.nativeTouch ? a.bindTouchHandlers() : a.bindHandlers()
        }, getMaxHeight: function() {
            for (i = this.maxHeight = 0; i < this.$items.length; i++) {
                var a = this.$items.eq(i);
                this.maxHeight += a.outerHeight();
                if (this.cutOff == i + 1)
                    break
            }
        }, bindTouchHandlers: function() {
            var a = this;
            a.$container.on("click.easyDropDown", function() {
                a.$select.focus()
            });
            a.$select.on({change: function() {
                    var c = d(this).find("option:selected"), b = c.text(), c = c.val();
                    a.$active.text(b);
                    "function" === typeof a.onChange && a.onChange.call(a.$select[0],
                            {title: b, value: c})
                }, focus: function() {
                    a.$container.addClass("focus")
                }, blur: function() {
                    a.$container.removeClass("focus")
                }})
        }, bindHandlers: function() {
            var a = this;
            a.query = "";
            a.$container.on({"click.easyDropDown": function() {
                    a.down || a.disabled ? a.close() : a.open()
                }, "mousemove.easyDropDown": function() {
                    a.keyboardMode && (a.keyboardMode = !1)
                }});
            d("body").on("click.easyDropDown." + a.id, function(c) {
                c = d(c.target);
                var b = a.wrapperClass.split(" ").join(".");
                !c.closest("." + b).length && a.down && a.close()
            });
            a.$items.on({"click.easyDropDown": function() {
                    var c =
                            d(this).index();
                    a.select(c);
                    a.$select.focus()
                }, "mouseover.easyDropDown": function() {
                    if (!a.keyboardMode) {
                        var c = d(this);
                        c.addClass("focus").siblings().removeClass("focus");
                        a.focusIndex = c.index()
                    }
                }, "mouseout.easyDropDown": function() {
                    a.keyboardMode || d(this).removeClass("focus")
                }});
            a.$select.on({"focus.easyDropDown": function() {
                    a.$container.addClass("focus");
                    a.inFocus = !0
                }, "blur.easyDropDown": function() {
                    a.$container.removeClass("focus");
                    a.inFocus = !1
                }, "keydown.easyDropDown": function(c) {
                    if (a.inFocus) {
                        a.keyboardMode =
                                !0;
                        var b = c.keyCode;
                        if (38 == b || 40 == b || 32 == b)
                            c.preventDefault(), 38 == b ? (a.focusIndex--, a.focusIndex = 0 > a.focusIndex ? a.$items.length - 1 : a.focusIndex) : 40 == b && (a.focusIndex++, a.focusIndex = a.focusIndex > a.$items.length - 1 ? 0 : a.focusIndex), a.down || a.open(), a.$items.removeClass("focus").eq(a.focusIndex).addClass("focus"), a.cutOff && a.scrollToView(), a.query = "";
                        if (a.down)
                            if (9 == b || 27 == b)
                                a.close();
                            else {
                                if (13 == b)
                                    return c.preventDefault(), a.select(a.focusIndex), a.close(), !1;
                                if (8 == b)
                                    return c.preventDefault(), a.query = a.query.slice(0,
                                            -1), a.search(), clearTimeout(a.resetQuery), !1;
                                38 != b && 40 != b && (c = String.fromCharCode(b), a.query += c, a.search(), clearTimeout(a.resetQuery))
                            }
                    }
                }, "keyup.easyDropDown": function() {
                    a.resetQuery = setTimeout(function() {
                        a.query = ""
                    }, 1200)
                }});
            a.$dropDown.on("scroll.easyDropDown", function(c) {
                a.$dropDown[0].scrollTop >= a.$dropDown[0].scrollHeight - a.maxHeight ? a.$container.addClass("bottom") : a.$container.removeClass("bottom")
            });
            if (a.$form.length)
                a.$form.on("reset.easyDropDown", function() {
                    a.$active.text(a.hasLabel ? a.label :
                            a.options[0].title)
                })
        }, unbindHandlers: function() {
            this.$container.add(this.$select).add(this.$items).add(this.$form).add(this.$dropDown).off(".easyDropDown");
            d("body").off("." + this.id)
        }, open: function() {
            var a = window.scrollY || document.documentElement.scrollTop, c = window.scrollX || document.documentElement.scrollLeft, b = this.notInViewport(a);
            this.closeAll();
            this.getMaxHeight();
            this.$select.focus();
            window.scrollTo(c, a + b);
            this.$container.addClass("open");
            this.$scrollWrapper.css("height", this.maxHeight + "px");
            this.down = !0
        }, close: function() {
            this.$container.removeClass("open");
            this.$scrollWrapper.css("height", "0px");
            this.focusIndex = this.selected.index;
            this.query = "";
            this.down = !1
        }, closeAll: function() {
            var a = Object.getPrototypeOf(this).instances, c;
            for (c in a)
                a[c].close()
        }, select: function(a) {
            "string" === typeof a && (a = this.$select.find("option[value=" + a + "]").index() - 1);
            var c = this.options[a], b = this.hasLabel ? a + 1 : a;
            this.$items.removeClass("active").eq(a).addClass("active");
            this.$active.text(c.title);
            this.$select.find("option").removeAttr("selected").eq(b).prop("selected",
                    !0).parent().trigger("change");
            this.selected = {index: a, title: c.title};
            this.focusIndex = i;
            "function" === typeof this.onChange && this.onChange.call(this.$select[0], {title: c.title, value: c.value})
        }, search: function() {
            var a = this, c = function(b) {
                a.focusIndex = b;
                a.$items.removeClass("focus").eq(a.focusIndex).addClass("focus");
                a.scrollToView()
            };
            for (i = 0; i < a.options.length; i++) {
                var b = a.options[i].title.toUpperCase();
                if (0 == b.indexOf(a.query)) {
                    c(i);
                    return
                }
            }
            for (i = 0; i < a.options.length; i++)
                if (b = a.options[i].title.toUpperCase(),
                        -1 < b.indexOf(a.query)) {
                    c(i);
                    break
                }
        }, scrollToView: function() {
            if (this.focusIndex >= this.cutOff) {
                var a = this.$items.eq(this.focusIndex).outerHeight() * (this.focusIndex + 1) - this.maxHeight;
                this.$dropDown.scrollTop(a)
            }
        }, notInViewport: function(a) {
            var c = a + (window.innerHeight || document.documentElement.clientHeight), b = this.$dropDown.offset().top + this.maxHeight;
            return b >= a && b <= c ? 0 : b - c + 5
        }, destroy: function() {
            this.unbindHandlers();
            this.$select.unwrap().siblings().remove();
            this.$select.unwrap();
            delete Object.getPrototypeOf(this).instances[this.$select[0].id]
        },
        disable: function() {
            this.disabled = !0;
            this.$container.addClass("disabled");
            this.$select.attr("disabled", !0);
            this.down || this.close()
        }, enable: function() {
            this.disabled = !1;
            this.$container.removeClass("disabled");
            this.$select.attr("disabled", !1)
        }};
    var f = function(a, c) {
        a.id = a.id ? a.id : "EasyDropDown" + ("00000" + (16777216 * Math.random() << 0).toString(16)).substr(-6).toUpperCase();
        var b = new e;
        b.instances[a.id] || (b.instances[a.id] = b, b.init(a, c))
    };
    d.fn.easyDropDown = function() {
        var a = arguments, c = [], b;
        b = this.each(function() {
            if (a &&
                    "string" === typeof a[0]) {
                var b = e.prototype.instances[this.id][a[0]](a[1], a[2]);
                b && c.push(b)
            } else
                f(this, a[0])
        });
        return c.length ? 1 < c.length ? c : c[0] : b
    };
    d(function() {
        "function" !== typeof Object.getPrototypeOf && (Object.getPrototypeOf = "object" === typeof "test".__proto__ ? function(a) {
            return a.__proto__
        } : function(a) {
            return a.constructor.prototype
        });
        d("select.easydropdown").each(function() {
            var a = d(this).attr("data-settings");
            settings = a ? d.parseJSON(a) : {};
            f(this, settings)
        })
    })
})(jQuery);