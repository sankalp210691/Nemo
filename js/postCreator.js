function Post() {
    var postLocation
    var set
    var postType
    var title
    var description
    var src
    var url
    var url_content_type
    var width
    var height
    var user_id
    var interest_tag = []
    var friend_tag = []
}

var gurl
var up = new Post()
function postCreator() {
//    scriptLoader('webcam', "", [[]], 0, null)
    var select = $("<select class='dropdown' id='set_chose'>")
    if (user_sets.length == 0) {
        $.ajax({
            url: "manager/SetsManager.php",
            type: "get",
            cache: true,
            data: "req=get_sets&get_preview=0&user_id=" + user_id,
            dataType: "json",
            beforeSend: function() {
                var div = $("<div>")
                div.addClass("white_overlay")
                div.html("<center><img src='img/massive_ajax_loader.gif'></center>")
                div.find("center").css({
                    "margin-top": "50px"
                })
                $("body").append(div)
            },
            success: function(sets) {
                $(".white_overlay").remove()
                var i, option = []
                var cho = $("<option value='-1'>")
                cho.html("Choose a set")
                select.html(cho)
                for (i = 0; i < sets.length; i++) {
                    user_sets[i] = sets[i]
                    option[i] = $("<option value='" + user_sets[i].id + "'>")
                    option[i].html(user_sets[i].name)
                    select.append(option[i])
                }
//                set.dropDownCheckList({
//                    source: user_sets,
//                    max_height: "150px",
//                    placeholder: "Choose sets",
//                    button: "Create set",
//                    buttonFunction: "callSetCreator"
//                })
                createWindow(select)
            }, error: function(e, f) {
                $(".white_overlay").remove()
                alertBox("Some error occured. Please try again later.")
            }
        })
    } else {
        var i, option = []
        var cho = $("<option value='-1'>")
        cho.html("Choose a set")
        select.html(cho)
        for (i = 0; i < user_sets.length; i++) {
            option[i] = $("<option value='" + user_sets[i].id + "'>")
            option[i].html(user_sets[i].name)
            select.append(option[i])
        }
//        set.dropDownCheckList({
//            source: user_sets,
//            max_height: "150px",
//            placeholder: "Choose sets",
//            "button": "Create set",
//            buttonFunction: "callSetCreator"
//        })
        createWindow(select)
    }
}

function createWindow(set) {
    var postCreator = new Box("post_creator", "90", "80")
    postCreator.createOverlay(1)
    postCreator.heading = "Create Post"
    var main_body = postCreator.createBox()

    var left_panel, middle_panel, right_panel
    left_panel = $("<div>")
    middle_panel = $("<div id='middle_panel'>")
    right_panel = $("<div id='right_panel'>")

    var po_op = "<ul class='po_op'><li id='ph_po' class='current_po_op'><img src='img/ph_po.png'><span>Photo</span></li><li id='vid_po'><img src='img/vd_po.png' style='margin-left:10px;margin-right:20px;height:51px;width:51px;'><span>Video</span></li><li id='wl_po'><img src='img/wl_po.png' style='width:51px;margin-left:10px;margin-right:20px'><span>Web Link</span></li><li id='pl_po'><img src='img/pl_po.png' style='width:51px;margin-left:10px;margin-right:20px'><span>Place</span></li><li id='pa_po'><img src='img/pn_po.jpg' style='width:51px;margin-left:10px;margin-right:20px'><span>Panorama</span></li><li id='po_po'><img src='img/po_po.jpg' style='width:51px;margin-left:10px;margin-right:20px'><span>Poll</span></li></ul>"
    left_panel.html(po_op)
    left_panel.css({
        "float": "left",
        "width": "20%",
        "height": "100%",
        "-webkit-box-shadow": "3px 0 3px #ccc",
        "-moz-box-shadow": "3px 0 3px #ccc",
        "box-shadow": "3px 0 3px #ccc",
        "-o-box-shadow": "3px 0 3px #ccc"
    })
    main_body.html(left_panel)
    $(".po_op li").width($(".po_op").parent().width() - 30)
    $(".po_op li").click(function() {
        if ($(this).hasClass("current_po_op") == false) {
            $(".po_op li").removeClass("current_po_op")
            $(this).addClass("current_po_op")
            var id = $(this).attr("id")
            $("#sugg_places_div").remove();
            $("#rpanel").show();
            switch (id) {
                case "ph_po":
                    middle_panel.ph_post(0)
                    break;
                case "vid_po":
                    middle_panel.vd_post(0)
                    break;
                case "wl_po":
                    wl_po(middle_panel, main_body)
                    break;
                case "pl_po":
                    middle_panel.pl_post(0)
                case "pn_po":
                    middle_panel.pn_post(0)
                default:
                    break;
            }
        }
    })

    right_panel.css({
        "float": "right",
        "width": "25%",
        "height": "100%",
        "overflow-x": "hidden",
        "-webkit-box-shadow": "-3px 0 3px #ccc",
        "-moz-box-shadow": "-3px 0 3px #ccc",
        "box-shadow": "-3px 0 3px #ccc",
        "-o-box-shadow": "-3px 0 3px #ccc"
    })
    main_body.append(right_panel)

    var title = $("<input id='post_title' type='text' placeholder='Title of post' class='mt1 ml1'>")
    var description = $("<textarea placeholder='A description of your post' class='mt1' id='post_desc'></textarea>")
    var interest_tagger = $("<div class='mt1 ml1' id='po_in_tg'>")
    var friend_tagger = $("<div class='mt1 ml1' id='po_fr_tg'>")
    var rpanel_main = $("<div id='rpanel'>")
    var post_input = $("<input id='post_input' type='button' class='bbutton' value='Post' style='width:80px;float:right;margin-top:10px;margin-right:10px;'>")

    var focus_title = "";
    title.focus(function() {
        focus_title = $.trim($(this).val())
    })

    title.blur(function() {
        var text = $.trim($(this).val())
        if (text.length == 0) {
            return;
        }
        if (text == focus_title) {
            return;
        }
        $.ajax({
            url: "manager/TagManager.php",
            dataType: "json",
            type: "get",
            data: "req=convert_tag&text=" + text,
            success: function(tags) {
                alert(JSON.stringify(tags))
            }
        })
    })

    post_input.click(function() {
        var postObject = new Post()
        postObject.user_id = user_id
        var type_id
        $(".po_op li").each(function() {
            if ($(this).hasClass("current_po_op")) {
                type_id = $(this).attr("id")
            }
        })
        if (type_id == "ph_po")
            postObject.postType = "photo"
        else if (type_id == "vid_po")
            postObject.postType = "video"
        else if (type_id == "wl_po")
            postObject.postType = "link"
        else if (type_id == "pl_po")
            postObject.postType = "place"

        postObject.set = set.val()

        postObject.title = $.trim(title.val())
        postObject.description = $.trim(description.val())

        var interest_tags = [], friend_tags = []
        var i = 0
        interest_tagger.find(".tag").each(function() {
            var t = $(this)
            interest_tags[i] = [t.children("input[type='hidden']").val(), $.trim(t.children(".val").html())]
            i++
        })
        i = 0
        friend_tagger.find("input[type='hidden']").each(function() {
            var t = $(this)
            friend_tags[i] = [t.children("input[type='hidden']").val(), $.trim(t.children(".val").html())]
            i++
        })
        postObject.interest_tag = interest_tags
        postObject.friend_tag = friend_tags
        var el

        if (postObject.postType == "video") {
            el = middle_panel.find(".po_con").children("embed")
            if (el.length > 0) {
                postObject.src = el.attr("src")
            } else {
                return
            }
        } else if (postObject.postType == "photo") {
            el = middle_panel.find(".po_con").find("img")
            if (el.length > 0) {
                postObject.src = el.attr("src")
                postObject.width = el.parent().children(".wi").val()
                postObject.height = el.parent().children(".hi").val()
            } else {
                return
            }
        } else if (postObject.postType == "link") {
            el = middle_panel.find(".po_lcon").find("img")
            if (el.length > 0) {
                postObject.url = gurl
                postObject.url_content_type = "photo"
                fetchAndPost(el.attr("src"), postObject, post_input, right_panel, postCreator)
                return
            } else {
                return
            }
        } else if (postObject.postType == "place") {
            el = middle_panel.find(".po_con").find("img");
            if (el.length > 0) {
                postObject.src = el.attr("data-src")
                postObject.height = el.parent().children(".hi").val();
                return
            } else {
                return
            }
        }

        $.ajax({
            url: "manager/PostManager.php",
            cache: true,
            type: "get",
            data: "req=create&post=" + encodeURIComponent(JSON.stringify(postObject)),
            beforeSend: function() {
                post_input.hide()
                var post_processing = $("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin-top:10px;margin-right:10px;'>")
                right_panel.append(post_processing)
            },
            success: function(data) {
                postCreator.closeBox()
                getPost($("#wall"), data)
                up = new Post()
            },
            error: function(e, f) {
                postCreator.closeBox()
                up = new Post()
                alertBox("Some error occured. Please try again later")
            }
        })
    })

    var create_set = $("<input type='button' class='bbutton' value='Create Set' style='width:100%;width:calc(100% - 1.875em);margin:10px 15px;'>")
    rpanel_main.html(create_set)
    create_set.click(function() {
        callSetCreator($("#post_creator"), $(this))
    })

    var cenor = $("<center style='margin-bottom:10px'>")
    cenor.html("<b>OR</b>")
    rpanel_main.append(cenor)

    var set_div = $("<div id='set_div' style='margin-left:15px;width:100%;width:calc(100% - 1.30em);display:table'>")
    rpanel_main.append(set_div)
    set_div.append(set)
    set.easyDropDown({cutOff: 7})

    rpanel_main.append(title)
    rpanel_main.append(description)
    rpanel_main.append(interest_tagger)
    rpanel_main.append(friend_tagger)
    right_panel.html(rpanel_main)
    rpanel_main.css({
        "width": "100%",
        "height": "90%",
        "overflow-y": "auto",
        "overflow-x": "hidden",
        "border-bottom": "1px solid #ccc"
    })
    right_panel.append(post_input)
    interest_tagger.addTagger("create_interest_tag", "Tag interests", "TagManager")
    friend_tagger.addTagger("create_friend_tag", "Tag your friends")
    centerFit(right_panel, [set, title, description, interest_tagger, friend_tagger], 15, "px")
    title.width(right_panel.width() - 35)

    middle_panel.css({
        "float": "left",
        "width": "54%",
        "height": "97%",
        "padding-left": "10px",
        "padding-top": "10px"
    })
    main_body.append(middle_panel)
    middle_panel.ph_post(0)

    function fetchAndPost(url, postObject, post_input, right_panel, postCreator) {
        $.ajax({
            url: "manager/PostManager.php",
            data: "req=fetchImage&url=" + url,
            type: "get",
            success: function(add) {
                if (add == -1) {
                    alertBox("Oh darn! There seems to be some problem with the URL.")
                    return
                }
                var img = $("<img>")
                img.attr("src", add)
                img.load(function() {
                    postObject.width = this.width
                    postObject.height = this.height
                    postObject.src = add
                    $.ajax({
                        url: "manager/PostManager.php",
                        cache: true,
                        type: "get",
                        data: "req=create&post=" + encodeURIComponent(JSON.stringify(postObject)),
                        beforeSend: function() {
                            post_input.hide()
                            var post_processing = $("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin-top:10px;margin-right:10px;'>")
                            right_panel.append(post_processing)
                        },
                        success: function(data) {
                            postCreator.closeBox()
                            getPost($("#wall"), data)
                            up = new Post()
                        },
                        error: function(e, f) {
                            postCreator.closeBox()
                            up = new Post()
                            alertBox("Some error occured. Please try again later")
                        }
                    })

                })
            }, error: function(e, f) {
                alertBox("Some error occured. Please try again later.")
            }
        })
    }
}

$.fn.ph_post = function(cur_op) {
    var middle_panel = $(this)
    var op_menu = $("<div id='op_menu'>")
    if (cur_op == 0)
        op_menu.html("<div class='cur_op' id='up'>Upload a Photo</div><div id='iu'>Image URL</div><div id='cal'>Create album</div>")
    else if (cur_op == 1)
        op_menu.html("<div id='up'>Upload a Photo</div><div class='cur_op' id='iu'>Image URL</div><div id='cal'>Create album</div>")
    else if (cur_op == 2)
        op_menu.html("<div id='up'>Upload a Photo</div><div id='iu'>Image URL</div><div class='cur_op' id='cal'>Create album</div>")
    middle_panel.html(op_menu)
    op_menu.width(middle_panel.width())

    if (up.postLocation == "up") {
        setPost(middle_panel, up)
    } else {
        middle_panel.designUploader()
    }

    $("#op_menu div").click(function() {
        if ($(this).hasClass("cur_op") == false) {
            $("#op_menu div").removeClass("cur_op")
            $(this).addClass("cur_op")
            var id = $(this).attr("id")
            if (id == "up")
            {
                if ($("#url_div").length > 0)
                    $("#url_div").remove()
                else if ($("#camdiv").length > 0)
                    $("#camdiv").remove()
                if (up.postLocation == "up") {
                    setPost(middle_panel, up)
                } else
                    middle_panel.designUploader()
            } else if (id == "iu") {
                if ($("#uploader").length > 0)
                    $("#uploader").remove()
                else if ($("#albumdiv").length > 0)
                    $("#albumdiv").remove()
                if ($("#result_area").length > 0) {
                    $("#result_area").remove()
                }

                var url_div = $("<div id='url_div'>")
                url_div.css({
                    "border": 0,
                    "width": 0.97 * middle_panel.width(),
                    "height": 0.8 * middle_panel.height(),
                    "margin-top": "50px"
                })

                var url_input = $("<input type='url'>")
                url_input.attr({
                    "id": "url_input",
                    "placeholder": "Enter the URL of the image"
                })
                url_input.css({
                    "width": "75%",
                    "padding-left": "5px",
                    "margin": "10px"
                })

                var url_ok = $("<input id='url_ok' type='button' class='bbutton' style='width:80px;' value='Go'>")

                var img_div = $("<div id='img_div' class='po_con' style='display:table;padding-left:20px'>")
                img_div.css({
                    "width": url_div.width() - 20,
                    "height": url_div.height() - 30
                })
                url_div.html(url_input)
                url_div.append(url_ok)
                url_div.append(img_div)
                middle_panel.append(url_div)

                $("#url_ok").click(function() {
                    var url = $.trim($("#url_input").val())
                    if (url.length > 0) {
                        $.ajax({
                            url: "manager/PostManager.php",
                            data: "req=fetchImage&url=" + url,
                            type: "get",
                            beforeSend: function() {
                                img_div.html("<center><img src='img/ajax_loader_horizontal.gif' style='margin-top:10px;'></center>")
                            },
                            success: function(add) {
                                if (add == -1) {
                                    alertBox("Oh darn! There seems to be some problem with the URL.")
                                    return
                                }
                                var img = $("<img>")
                                img.attr("src", add)
                                var postObject = new Post()
                                postObject.type = "photo"
                                postObject.element = img
                                postObject.src = add
                                img.load(function() {
                                    postObject.width = this.width
                                    postObject.height = this.height
                                    img_div.find("center").remove()
                                    setPost(img_div, postObject)
                                })
                            }, error: function(e, f) {
                                alertBox("Some error occured. Please try again later.")
                            }
                        })
                    }
                })
            } else if (id == "cal") {
                alert(3)
            }
        }
    })
}

$.fn.vd_post = function(cur_op) {
    var middle_panel = $(this)
    var op_menu = $("<div id='op_menu'>")
    if (cur_op == 0)
        op_menu.html("<div id='vu' class='cur_op' style='width:50%;'>Video URL</div><div id='sy' style='width:50%;'>Search Youtube</div>")
    else if (cur_op == 1)
        op_menu.html("<div id='vu style='width:50%;'>Video URL</div><div id='sy' class='cur_op' style='width:50%;'>Search Youtube</div>")
    middle_panel.html(op_menu)
    op_menu.width(middle_panel.width())

    urlFetcher(middle_panel)
    $("#op_menu div").click(function() {
        if ($(this).hasClass("cur_op") == false) {
            $("#op_menu div").removeClass("cur_op")
            $(this).addClass("cur_op")
            var id = $(this).attr("id")
            if (id == "vu") {
                if ($("#youtube_searcher").length > 0)
                    $("#youtube_searcher").remove()
                urlFetcher(middle_panel)
            } else if (id == "sy") {
                $("#url_fetcher").remove()
                var youtubeSearcher = $("<div id='youtube_searcher' style='margin-top:50px;'>")
                youtubeSearcher.css({
                    "width": "97%",
                    "height": "80%"
                })
                var search_input = $("<input type='text' id='search_input'>")
                var go = $("<input type='button' value='Search' class='bbutton' style='width:80px'>")
                search_input.attr({
                    "placeholder": "Search Youtube videos here"
                })
                search_input.css({
                    "width": "75%",
                    "padding-left": "5px",
                    "margin": "10px"
                })
                var result_div = $("<div id='result_div'>")
                result_div.css({
                    "width": "100%",
                    "height": "100%",
                    "margin-left": "5px",
                    "overflow": "auto"
                })
                youtubeSearcher.html(search_input)
                youtubeSearcher.append(go)
                youtubeSearcher.append(result_div)
                middle_panel.append(youtubeSearcher)
                search_input.keyup(function(e) {
                    if (e.keyCode == 13) {
                        go.click()
                    }
                })
                go.click(function() {
                    var search = search_input.val().trim(), i = 0
                    var keyword = encodeURIComponent(search)
                    if ($(".po_con").length > 0) {
                        $(".po_con").parent().remove()
                        result_div.show('slide', {
                            direction: 'left'
                        }, 300)
                    }
                    // Youtube API 
                    var yt_url = 'https://gdata.youtube.com/feeds/api/videos?q=' + keyword + '&format=5&max-results=40&v=2&alt=jsonc';
                    $.ajax
                            ({
                                type: "GET",
                                url: yt_url,
                                dataType: "jsonp",
                                beforeSend: function() {
                                    result_div.html("<center><img src='img/massive_ajax_loader.gif' style='width:80%'></center>")
                                },
                                success: function(response)
                                {
                                    if (response.data.items)
                                    {
                                        result_div.html("")
                                        $.each(response.data.items, function(i, data)
                                        {
                                            var video_id = data.id;
                                            var video_title = data.title;
                                            var video_viewCount = data.viewCount;
                                            var f = $("<img src='http://img.youtube.com/vi/" + video_id + "/1.jpg' class='img_play'>")
                                            f.addClass("cp")
                                            result_div.append(f) // Result
                                            var w = result_div.width() / 6
                                            f.css({
                                                "margin": "3px",
                                                "width": w
                                            })
                                            f.click(function() {
                                                var url = "http://www.youtube.com/watch?v=" + video_id
                                                var vd = $("<div>")
                                                var video_div = $("<div class='po_con'>")
                                                vd.css({
                                                    "width": "100%",
                                                    "height": "400px"
                                                })
                                                video_div.css({
                                                    "width": "100%",
                                                    "height": "400px",
                                                    "margin-top": "5px"
                                                })
                                                var back = $("<a href='#' style='margin-left:10px;text-decoration:underline'>")
                                                back.html("Back to results")
                                                vd.html(back)
                                                back.click(function() {
                                                    $(".po_con").parent().remove()
                                                    result_div.show('slide', {
                                                        direction: 'left'
                                                    }, 300)
                                                })
                                                vd.append(video_div)
                                                youtubeSearcher.append(vd)
                                                result_div.hide('slide', {
                                                    direction: 'left'
                                                }, 300, function() {
                                                    embedVideo(url, video_div)
                                                })
                                            })
                                        })
                                    }
                                    else
                                    {
                                        $("#result_div").html("<div id='no'>No video found</div>");
                                    }
                                }
                            });
                })
            }
        }
    })
}

$.fn.designUploader = function() {
    var middle_panel = $(this)
    if ($("#uploader").length > 0)
        $("#uploader").remove()
    var uploader = $("<div>")
    uploader.attr({
        "id": "uploader",
        "ondrop": "drop(event,1)",
        "ondragover": "allowDrop(event)"
    })
    uploader.addClass("mp_html")
    var uploader_msg = $("<div>")
    uploader_msg.html("<center>Drag & Drop the photo here<br>OR<br><input type='button' style='width:200px' class='bbutton' value='Select from your computer'></center>")
    var sim_upldr = $("<input type='file' id='sim_upldr' size='6'>")
    uploader_msg.find("center").append(sim_upldr)
    uploader.html(uploader_msg)
    middle_panel.append(uploader)

    sim_upldr.change(function() {
        var file = document.getElementById("sim_upldr").files[0]
        var loading_div = $("<div>")
        var fileName = $("<div>")
        fileName.html(file.name)
        var progressBox = $("<div>")
        progressBox.attr("class", "progressBox")
        var progressBar = $("<div>")
        progressBar.attr("class", "progressBar")
        loading_div.html(fileName)
        loading_div.append(progressBox)
        progressBox.html(progressBar)
        $("#uploader center").html(loading_div)
        handleFiles(file, 1, 0, [file], middle_panel, "photo");
    })
}

$.fn.pl_post = function(cur_op) {
    var middle_panel = $(this)
    middle_panel.html("")
    middle_panel.css("position", "relative")

    var place_input = $("<input type='text' id='place_input' class='controls' placeholder='Search places'>")
    var map_canvas = $("<div id='map_canvas'>")
    map_canvas.height(middle_panel.height() - 5);
    middle_panel.html(place_input)
    middle_panel.append(map_canvas)

    place_input.keyup(function(e) {
        if (e.keyCode != 13) {
            if ($("#sugglistdiv").length == 0) {
                var sugglist = $("<div id='sugglistdiv' class='uni_shadow_dark'>")
                var ul = $("<ul id='sugglist'>");
                sugglist.html(ul);
                middle_panel.prepend(sugglist);
            }
            if (!((e.keyCode >= 48 && e.keyCode <= 90) || e.keyCode == 32 || e.keyCode == 8 || e.keyCode == 13 || (e.keyCode >= 96 && e.keyCode <= 111) || (e.keyCode >= 186 && e.keyCode <= 192 || (e.keyCode >= 219 && e.keyCode <= 222)))) {
                return;
            }
            if (e.keyCode == 27) {
                $("#sugglistdiv").remove();
                return;
            }
            $('#sugglist').html("");
            var service = new google.maps.places.AutocompleteService();
            service.getQueryPredictions({input: $("#place_input").val()}, callback);

            function callback(predictions, status) {
                if (status != google.maps.places.PlacesServiceStatus.OK) {
//                alert(status);
                    return;
                }
                $('#sugglist').html("")
                for (var i = 0, prediction; prediction = predictions[i]; i++) {
                    var li = $("<li>");
                    li.html(prediction.description);
                    $('#sugglist').append(li);
                    li.click(function() {
                        $("#place_input").val($(this).html());
                        $("#place_input").focus();
                        $("#sugglistdiv").remove();
                    })
                }
            }
        } else {
            $("#sugglistdiv").remove();
        }
    })

    loadGoogleMapScript()
//    initializeGoogleMap()
}

function loadGoogleMapScript() {
    if ($("#mapapi").length == 0) {
        var script = document.createElement('script');
        script.id = "mapapi"
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places&' +
                'callback=initializeGoogleMap';
        document.body.appendChild(script);
    } else {
        initializeGoogleMap()
    }
}

function initializeGoogleMap() {
    var map = new google.maps.Map($('#map_canvas').get(0), {
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude,
                    position.coords.longitude);

            var infowindow = new google.maps.InfoWindow({
                map: map,
                position: pos,
                content: 'Location found using HTML5.'
            });

            map.setCenter(pos);
        }, function() {
            handleNoGeolocation(true);
        });
    } else {
        // Browser doesn't support Geolocation
        handleNoGeolocation(false);
    }

    function handleNoGeolocation(errorFlag) {
        if (errorFlag) {
            var content = 'Error: The Geolocation service failed.';
        } else {
            var content = 'Error: Your browser doesn\'t support geolocation.';
        }

        defaultBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(21.652693, 67.705383),
                new google.maps.LatLng(24.347762, 94.012146));
        map.fitBounds(defaultBounds);
        return
    }

// Create the search box and link it to the UI element.
    var input = /** @type {HTMLInputElement} */(
            $('#place_input').get(0));
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    var searchBox = new google.maps.places.SearchBox(
            /** @type {HTMLInputElement} */(input));

// [START region_getplaces]
// Listen for the event fired when the user selects an item from the
// pick list. Retrieve the matching places for that item.
    google.maps.event.addListener(searchBox, 'places_changed', function() {
        var places = searchBox.getPlaces();

        // For each place, get the icon, place name, and location.
        var markers = [];
        var bounds = new google.maps.LatLngBounds();

        $("#rpanel,#post_input").fadeOut("1000", function() {
            if ($("#sugg_places_div").length > 0)
                $("#sugg_places_div").remove();
            var sugg_places_div = $("<div id='sugg_places_div'>")
            var list = $("<div>")
            list.html("<h4 style='border-bottom:1px solid #ccc;padding:5px 10px'>Choose one of the places</h4>");
            sugg_places_div.html(list);
            $("#right_panel").prepend(sugg_places_div);
            for (var i = 0, place; place = places[i]; i++) {
                var sugg;
                if (i < 5)
                    sugg = $("<div class='sugg' id='sugg" + i + "'>");
                else
                    sugg = $("<div class='sugg' id='sugg" + i + "' style='display:none;'>");
                sugg.html("<div style='float:left;width:20px;margin-right:5px;'><img src='" + places[i].icon + "' style='width:20px;height:20px;'></div>");
                var dsc = $("<div style='float:left;width:100%;width:calc(100% - 2.5em)'>");
                sugg.append(dsc);
                dsc.append("<b style='color:#007dff;'>" + places[i].name + "</b><br>");
                dsc.append("<span style='font-size:15px;'>" + places[i].formatted_address + "</span>");
                list.append(sugg);

                sugg.click(function() {
                    var index = $(this).attr("id").substr(4);
                    var image = {
                        url: $(this).find("img").attr("src"),
                        size: new google.maps.Size(100, 100),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    var marker = new google.maps.Marker({
                        map: map,
                        icon: image,
                        title: places[index].name,
                        position: places[index].geometry.location
                    });
                    map.panTo(marker.position);
                    map.setZoom(17);

                    bounds.extend(marker.position);
                    list.fadeOut("1000", function() {
                        var pic_option_div = $("<div id='cod'>"), i;
                        sugg_places_div.append(pic_option_div);

                        var coordinate_string = "" + places[index].geometry.location;
                        coordinate_string = coordinate_string.substr(1, coordinate_string.length - 2);
                        pic_option_div.html("<h4 style='border-bottom:1px solid #ccc;padding:5px 10px'>Choose map or a pic</h4>");

                        var mappic = $("<div data-coordinate='" + coordinate_string + "' class='codpic uni_shadow_light' style=\"width:280px;height:180px;background-image:url('http://maps.googleapis.com/maps/api/staticmap?center=" + coordinate_string + "&markers=" + coordinate_string + "&zoom=17&size=280x180&sensor=false')\">");
                        pic_option_div.append(mappic);

                        var uploadpic = $("<div style='position:relative;' class='codpic uni_shadow_light'>")
                        uploadpic.html("<p id='upldr_prompt'>Upload a pic</p>");
                        var file = $("<input id='ppu' type='file' onchange='uploadplacepic(\"" + places[index].name + "\")' style='opacity:0;cursor:pointer;position:absolute;top:0;left:0;width:100%;height:100%;'>")
                        uploadpic.append(file);
                        pic_option_div.append(uploadpic);
                        $("#post_title").val(places[index].name)

                        mappic.click(function() {
                            var img = $("<img>")
                            img.attr("src", "http://maps.googleapis.com/maps/api/staticmap?center=" + $(this).attr("data-coordinate") + "&markers=" + $(this).attr("data-coordinate") + "&zoom=17&size=450x400&sensor=false")

                            var result_area = $("<div id='result_area'>")
                            var post_area = $("<div id='post_area' class='po_con'>")
                            var middle_panel = $("#middle_panel");

                            $("#middle_panel").html("");
                            result_area.html(post_area)
                            middle_panel.append(result_area)
                            result_area.css({
                                "width": middle_panel.width(),
                                "height": 0.98 * middle_panel.height(),
                                "margin-left": "-5px"
                            })

                            post_area.css({
                                "width": middle_panel.width(),
                                "height": result_area.height()
                            })
                            img.addClass("uni_shadow_dark")
                            post_area.fitImage(img, 450, 400, "both")

                            $("#sugg_places_div").fadeOut("1000", function() {
                                $("#rpanel,#post_input").fadeIn("1000");
                            })
                        });
                    })
                })
            }
            if (i >= 5) {
                list.append("<div id='lmpr'>Load more results</div>")
                $("#lmpr").click(function() {
                    var j;
                    for (j = 5; j < i; j++) {
                        $("#sugg" + j).show();
                    }
                    $("#lmpr").remove();
                })
            }
        })

        for (var i = 0, place; place = places[i]; i++) {
            var image = {
                url: place.icon,
                size: new google.maps.Size(100, 100),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            var marker = new google.maps.Marker({
                map: map,
                icon: image,
                title: place.name,
                position: place.geometry.location
            });

            markers.push(marker);
            map.panTo(marker.position);
            map.setZoom(17);

            bounds.extend(place.geometry.location);
        }

        map.fitBounds(bounds);
    });
// [END region_getplaces]

// Bias the SearchBox results towards places that are within the bounds of the
// current map's viewport.
    google.maps.event.addListener(map, 'bounds_changed', function() {
        var bounds = map.getBounds();
        searchBox.setBounds(bounds);
    });
}

function uploadplacepic(place_name) {
    var file = document.getElementById("ppu").files[0]
    if (file.name == null) {
        return;
    } else {
        $("#upldr_prompt").replaceWith("<center><img data-place-name='" + place_name + "' id='plldr' src='img/ajax_loader_horizontal.gif'></center>");
    }
    handleFiles(file, 1, 0, [file], $("#middle_panel"), "place");
}

$.fn.pn_po = function(cur_op){
    var middle_panel = $(this);
    
}

function wl_po(middle_panel, main_body) {
    var op_menu = $("<div id='op_menu'>")
    var url_input = $("<input type='url'>")
    url_input.attr({
        "id": "url_input",
        "placeholder": "Enter the URL of the webpage"
    })
    url_input.css({
        "padding-left": "5px",
        "margin": "10px"
    })
    op_menu.html(url_input)
    middle_panel.html(op_menu)
    op_menu.width(middle_panel.width())
    url_input.width(op_menu.width() - 30)

    var post_div = $("<div id='img_div' class='po_con'>")
    post_div.css({
        "width": op_menu.width(),
        "height": middle_panel.height() - 50,
        "margin-top": "50px",
        "overflow-y": "scroll",
        "overflow-x": "hidden"
    })
    middle_panel.append(post_div)
    url_input.bind("paste", function() {
        setTimeout(function() {
            var url = url_input.val().trim();
            if (url.length > 0) {
                $.ajax({
                    url: "req/getImagesFromPage.php",
                    type: "post",
                    dataType: "json",
                    data: "url=" + url,
                    beforeSend: function() {
                        url_input.prop("disabled", true)
                        post_div.html("<center><img src='img/ajax_loader_horizontal.gif' style='margin-top:10px;'></center>")
                    }, success: function(array) {
                        url_input.prop("disabled", false)
                        gurl = url
                        post_div.html("")
                        if (array.img.length == 0) {
                            alertBox("We are afraid <a href='" + url + "' target='_blank'>your link</a> has denied our request")
                            return
                        }
                        if (array.meta.length != 0) {
                            $("#post_title").val(array.meta.title)
                            $("#post_desc").val(array.meta.description)
                            $.ajax({
                                url: "manager/TagManager.php",
                                dataType: "json",
                                type: "get",
                                data: "req=convert_tag&text=" + array.meta.title,
                                success: function(tags) {
                                    alert(JSON.stringify(tags))
                                }
                            })
                        }
                        var i, k = 0, array_size = array.img.length, image = [], valid_image = []
                        var cont = $("<div style='width:1px;height:1px;overflow:hidden'>")
                        post_div.prepend(cont)
                        for (i = 0; i < array_size; i++) {
                            image[i] = $("<img class='sample'>");
                            image[i].attr("src", array.img[i]);
                            cont.append(image[i]);
                            image[i].load(function() {
                                if ($(this).width() > 100 && $(this).height() > 100) {
                                    valid_image[k] = $(this);
                                    valid_image[k].addClass("vsample")
                                    post_div.append(valid_image[k])
                                    valid_image[k].click(function() {
                                        var url = $(this).attr("src")
                                        $(".vsample").fadeOut("slow")
                                        var po_con = $("<div class='po_lcon'>")
                                        var back = $("<a href='#' style='margin-left:10px;text-decoration:underline;display:block'>")
                                        back.html("Back to results")
                                        back.click(function() {
                                            back.remove()
                                            $(".po_lcon").remove()
                                            $(".vsample").fadeIn("slow")
                                        })
                                        post_div.append(back)
                                        post_div.append(po_con)
                                        po_con.append("<center><img src='" + url + "' class='sample_dec' style='max-width:100%'></center>")
                                    })
                                    k++;
                                }
                            })
                        }
                        post_div.imagesLoaded(function() {
                            post_div.masonry()
                            post_div.height(middle_panel.height() - 50)
                        })
                    }, error: function(e, f) {
                        url_input.prop("disabled", false)
                        alertBox("Some error occured. Please try agian later.")
                    }
                })
            }
        }, 5)
    })
    main_body.append(middle_panel)
}

function urlFetcher(middle_panel) {
    var url_fetcher = $("<div id='url_fetcher' style='margin-top:50px;'>")
    var url_input = $("<input id='url_input' type='url'>")
    url_input.attr({
        "placeholder": "Enter the URL of the video"
    })
    url_input.css({
        "width": "90%",
        "padding-left": "5px",
        "margin": "10px"
    })
    var video_div = $("<div id='video_div' style='width:100%;' class='po_con'>")

    url_fetcher.html(url_input)
    url_fetcher.append(video_div)
    middle_panel.append(url_fetcher)

    url_input.bind("paste", function() {
        setTimeout(function() {
            var url = $("#url_input").val().trim()
            if (url.length > 0) {
                embedVideo(url, video_div)
            }
        }, 5)
    })
}

function embedVideo(url, video_div) {
    var embed, video_code
    embed = $("<embed>")
    embed.attr({
        "id": "video_post",
        "wmode": "transparent",
        "allowfullscreen": "true",
        "type": "application/x-shockwave-flash",
        "width": "92%",
        "height": "350px",
        "background": "black"
    })
    embed.css({
        "margin-left": "10px"
    })
    video_div.html("<center><img src='img/massive_ajax_loader.gif' style='width:80%'></center>")
    if (isYoutubeURL(url)) {
        video_code = validateYoutubeURL(url)
        if (video_code != false) {
            url = "https://www.youtube.com/v/" + video_code
            embed.attr("src", url)
            video_div.html(embed)
        } else {
            video_div.html("<span style='margin-left:10px;color:red;font-weight:bold;'>Invalid URL</span>")
        }
    } else if (isVimeoURL(url)) {
        video_code = validateVimeoURL(url)
        if (video_code != false) {
            var iframe = $("<iframe src='http://player.vimeo.com/video/" + video_code + "?portrait=0&color=333' webkitAllowFullScreen mozallowfullscreen allowFullScreen>")
            iframe.attr({
                "id": "video_post",
                "width": "92%",
                "height": "350px",
                "background": "black"
            })
            iframe.css({
                "margin-left": "10px"
            })
            video_div.html(iframe)
        } else {
            video_div.html("<span style='margin-left:10px;color:red;font-weight:bold;'>Invalid URL</span>")
        }
    } else {
        video_div.html("<span style='margin-left:10px;color:#222;font-weight:bold;'>We currently support only Youtube & Vimeo videos</span>")
    }
}

function isYoutubeURL(url) {
    var valid = (url.match(/youtu\.be/i) || url.match(/youtube\.com\/watch/i))
    if (valid == null) {
        if (url.indexOf("http://www.youtube.com/embed/") > -1)
            return true
        else
            return false
    } else {
        return true
    }
}

function isVimeoURL(url) {
    return (url.match(/vimeo\.com/i))
}

function validateYoutubeURL(url) {
    var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/
    var match = url.match(regExp)
    if (match.length > 0 && match[2].length == 11) {
        return match[2];
    } else {
        return false
    }
}

function validateVimeoURL(url) {
    var arr = url.split("/")
    if (arr.length > 0)
        return arr[arr.length - 1]
    else
        return false
}

//Implementing Drag & Drop
function drag(ev)
{
    ev.dataTransfer.setData("Text", ev.target.id)
}

function allowDrop(ev, e)
{
    ev.preventDefault()
}

function drop(evt, allowed_count)
{
    evt.preventDefault()
    var files = evt.dataTransfer.files
    var count = files.length
    var i

    if (count > 0) {
        if (count > allowed_count)
            count = allowed_count

        for (i = 0; i < count; i++) {
            var file = files[i]

            if (count == 1) {
                var loading_div = $("<div>")
                var fileName = $("<div>")
                fileName.html(file.name)
                var progressBox = $("<div>")
                progressBox.attr("class", "progressBox")
                var progressBar = $("<div>")
                progressBar.attr("class", "progressBar")
                loading_div.html(fileName)
                loading_div.append(progressBox)
                progressBox.html(progressBar)
                $("#uploader center").html(loading_div)
            }
        }
    }
    handleFiles(files[0], count, 0, files, $("#uploader").parent(), "photo");
}

function handleFiles(file, count, i, files, middle_panel, post_type) {
    var file_size = file.size
    var file_type = file.type
    var f = validateImage(file_type, file_size)
    if (f == -1) {
        if (post_type == "photo") {
            if ($(".error_message").length > 0)
                $(".error_message").remove()
            middle_panel.find("center").html("<span style='color:red' class='error_message'>Invalid file format</span>")
            if (i < count) {
                i++
                handleFiles(files[i], count, i, files, middle_panel, post_type) //change this for multiple files
            }
        } else if (post_type == "place") {
            $("#plldr").replaceWith("<p id='upldr_prompt'>Upload a pic</p>");
            alertBox("Invalid file format");
        }
        return;
    } else if (f == -2) {
        if (post_type == "photo") {
            if ($(".error_message").length > 0)
                $(".error_message").remove()
            middle_panel.find("center").html("<span style='color:red' class='error_message'>File size limit it 4.5MB</span>")
            if (i < count) {
                i++
                handleFiles(files[i], count, i, files, middle_panel, post_type)   //change this for multiple files
            }
        } else if (post_type == "place") {
            $("#plldr").replaceWith("<p id='upldr_prompt'>Upload a pic</p>");
            alertBox("File size limit it 4.5MB");
        }
        return
    }

    var reader = new FileReader();
    // init the reader event handlers
    reader.onload = (function(e) {
        var fileName = file.name
        var result = e.target.result
        $.ajax({
            url: "req/uploader.php",
            type: "post",
            cache: false,
            dataType: "json",
            data: "req=upload&name=" + fileName + "&value=" + result + "&record=1",
            success: function(data) {
                if (data[0] == -1) {
                    alertBox("Error in uploading file. Please try again later.")
                    return
                }
                var img = $("<img>")
                img.attr("src", data.photo_address)
                if (post_type == "photo") {
                    up.postLocation = "up"
                    up.type = "photo"
                    up.width = data.photo_width
                    up.height = data.photo_height
                    up.src = data.photo_address
                    up.element = img
                    setPost(middle_panel, up);
                } else if (post_type == "place") {
                    img.attr("data-src", data.photo_address);

                    var result_area = $("<div id='result_area'>")
                    var post_area = $("<div id='post_area' class='po_con'>")

                    result_area.html(post_area)
                    middle_panel.html(result_area)
                    result_area.css({
                        "width": middle_panel.width(),
                        "height": 0.98 * middle_panel.height(),
                        "margin-left": "-5px"
                    })

                    post_area.css({
                        "width": middle_panel.width(),
                        "height": result_area.height()
                    })
                    img.addClass("uni_shadow_dark")

                    $("#sugg_places_div").fadeOut("1000", function() {
                        $("#rpanel,#post_input").fadeIn("1000");
                        post_area.fitImage(img, data.photo_width, data.photo_height, "both")
                    })
                }
            }
        })
    });
    reader.onprogress = (function(event) {
        if (event.lengthComputable) {
            if (post_type == "photo") {
                var progress = Math.round((event.loaded / event.total) * 100);
                $(".progressBar").css("width", progress + "%")
            }
        }
    });
    reader.onloadend = (function(event) {
        if (post_type == "photo") {
            var progressBox_width = $(".progressBox").css("width")
            $(".progressBar").css("width", progressBox_width)
            if (i < count) {
                i++
                handleFiles(files[i], count, i, files, post_type);
            }
        }
    });
    reader.readAsDataURL(file);
}

function setPost(middle_panel, postObject) {
    if ($(".mp_html").length > 0)
        $(".mp_html").remove();
    if ($("#result_area").length > 0)
        $("#result_area").remove();
    var result_area = $("<div id='result_area'>")
    var post_area = $("<div id='post_area' class='po_con'>");
    var option_area = $("<div id='option_area'>");
    var options = $("<ul class='linear_list cp post_op_area'>");
    var remove = $("<li>");
    remove.html("Remove");
    options.html(remove);

    remove.click(function() {
        var id = $(".cur_op").attr("id")
        if (id == "up") {
            up = new Post();
            result_area.remove();
            $.ajax({
                url: "req/uploader.php",
                cache: false,
                type: "post",
                data: "req=delete&path=" + postObject.src
            })
            middle_panel.designUploader();
        } else if (id == "iu") {
            result_area.remove();
        } else if (id == "vu") {
            $("#video_div").remove();
        }
    })

    option_area.html(options);
    result_area.html(post_area);
    result_area.append(option_area);
    middle_panel.append(result_area);
    result_area.css({
        "width": middle_panel.width(),
        "height": 0.98 * middle_panel.height(),
        "margin-left": "-5px"
    });

    option_area.css({
        "width": middle_panel.width(),
        "height": "30px"
    });

    post_area.css({
        "width": middle_panel.width(),
        "height": result_area.height() - option_area.height()
    });
    var e = postObject.element;
    e.addClass("uni_shadow_dark");
    if (postObject.type == "photo")
        post_area.fitImage(e, postObject.width, postObject.height, "both");
    else
        post_area.append(e);
}

function validateImage(file_type, file_size) {
    if (file_type != "image/png" && file_type != "image/bmp" && file_type != "image/jpeg") {
        return -1;
    }
    if (file_size > 4500000) {
        return -2;
    }
    return 1;
}
//Drag & Drop Ends