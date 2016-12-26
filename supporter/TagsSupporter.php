<?php

function getTagsByKey($key) {
    if (strlen($key) == 3) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $con = $db_connection->getCon();
        $query = "select id,name,popularity from tags where name like ? order by popularity desc";
        $statement = $con->prepare($query);
        $key = $key . "%";
        $statement->bind_param('s', $key);
        $statement->execute();
        $statement->bind_result($id, $name, $popularity);
        $tags = array();
        $i = 0;
        while ($statement->fetch()) {
            $tags[$i] = array(
                "id" => $id,
                "name" => $name,
                "popularity" => $popularity
            );
            $i++;
        }
        $statement->close();
        $db_connection->mysqli_connect_close();
        return $tags;
    } else {
        return array();
    }
}

function getUserTags($user_id, $persistent_connection) {
    if (strlen($user_id) == 0 || $user_id < 1) {
        return array(-1);
    } else {
        if ($persistent_connection == null) {
            $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
            $persistent_connection = $db_connection->getCon();
        }
        $query = "select t.id,t.name from tags t join post_tags pt on pt.tag_id=t.id join post p on p.id = pt.post_id where p.user_id=? group by t.id order by t.popularity desc";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $user_id);
        $statement->bind_result($id, $name);
        $statement->execute();
        $i = 0;
        $tags = array();
        while ($statement->fetch()) {
            $tags[$i] = array(
                "id" => $id,
                "name" => $name
            );
            $i++;
        }
        if ($persistent_connection == null) {
            $db_connection->mysqli_connect_close();
        }
        return $tags;
    }
}

function getTagPosts($tag_id, $order, $post_type_requested, $start, $limit, $persistent_connection) {
    if (strlen($tag_id) == 0 || strlen($order) == 0 || strlen($post_type_requested) == 0 || strlen($start) == 0 || strlen($limit) == 0) {
        return array();
    }
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }
    $posts = array();
    if ($post_type_requested == "all") {
        $query = "select p.id,p.title,p.description,p.src,p.type from post p join post_tags pt on pt.post_id=p.id where pt.tag_id=? and p.type<>\"share\"";
        if ($order == "popular")
            $query.=" order by p.score desc limit ? , ?";
        else if ($order == "recent")
            $query.=" order by p.id desc limit ? , ?";
        else if ($order == "most_liked")
            $query.= " order by likes desc limit ? , ?";
        else if ($order == "most_shared")
            $query.= " order by shares desc limit ? , ?";
        else if ($order == "most_commented")
            $query.= " order by comments desc limit ? , ?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("iii", $tag_id, $start, $limit);
        $statement->bind_result($post_id, $title, $description, $src, $type);
        $statement->execute();
        $i = 0;
        while ($statement->fetch()) {
            if ($type == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            }
            $posts[$i] = array(
                "id" => $post_id,
                "title" => $title,
                "description" => $description,
                "src" => $src,
                "post_type" => $type
            );
            $i++;
        }
    } else if ($post_type_requested == "photo" || $post_type_requested == "video" || $post_type_requested == "place" || $post_type_requested == "panorama" || $post_type_requested == "link") {
        $query = "select p.id,p.title,p.description,p.src from post p join post_tags pt on pt.post_id=p.id where pt.tag_id=? and p.type=?";
        if ($order == "popular")
            $query.=" order by p.score desc limit ? , ?";
        else if ($order == "recent")
            $query.=" order by p.id desc limit ? , ?";
        else if ($order == "most_liked")
            $query.= " order by likes desc limit ? , ?";
        else if ($order == "most_shared")
            $query.= " order by shares desc limit ? , ?";
        else if ($order == "most_commented")
            $query.= " order by comments desc limit ? , ?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("isii", $tag_id, $post_type_requested, $start, $limit);
        $statement->bind_result($post_id, $title, $description, $src);
        $statement->execute();
        $posts = array();
        $i = 0;
        while ($statement->fetch()) {
            if ($post_type_requested == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            }
            $posts[$i] = array(
                "id" => $post_id,
                "title" => $title,
                "description" => $description,
                "src" => $src,
                "post_type" => $post_type_requested
            );
            $i++;
        }
    }
    if ($persistent_connection == null) {
        $db_connection->mysqli_connect_close();
    }
    return $posts;
}

function getTopTagUsers($tag_id, $start, $limit, $persistent_connection) {
    if (strlen($tag_id) == 0 || strlen($start) == 0 || strlen($limit) == 0) {
        return array();
    }
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }
    $query = "select distinct u.id as user_id,concat(u.first_name,\" \",u.last_name) as user_name,u.profile_pic as profile_pic from user u join post p on p.user_id=u.id join post_tags pt on pt.post_id=p.id where pt.tag_id=? order by p.score desc limit ? , ?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("iii", $tag_id, $start, $limit);
    $statement->bind_result($user_id, $name, $profile_pic);
    $statement->execute();
    $i = 0;
    $users = array();
    while ($statement->fetch()) {
        $profile_pic = getBlurPicAddress($profile_pic);
        $users[$i] = array(
            "id" => $user_id,
            "name" => $name,
            "profile_pic" => $profile_pic
        );
        $i++;
    }
    if ($persistent_connection == null) {
        $db_connection->mysqli_connect_close();
    }
    return $users;
}

function getTopTagSets($tag_id, $start, $limit, $persistent_connection) {
    if (strlen($tag_id) == 0 || strlen($start) == 0 || strlen($limit) == 0) {
        return array();
    }
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }
    $query = "select s.id,s.name,avg(p.score) as avg from sets s 
join post p on p.set_id=s.id 
join post_tags pt on pt.post_id=p.id 
where pt.tag_id=?
group by s.id
order by avg desc
limit ? , ?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("iii", $tag_id, $start, $limit);
    $statement->bind_result($set_id, $name, $avg_score);
    $statement->execute();
    $i = 0;
    $sets = array();
    while ($statement->fetch()) {
        $sets[$i] = array(
            "id" => $set_id,
            "name" => $name,
            "avg_score" => $avg_score
        );
        $i++;
    }
    if ($persistent_connection == null) {
        $db_connection->mysqli_connect_close();
    }
    return $sets;
}

function getAssociatedTags($tag_id, $start, $limit, $persistent_connection) {
    if (strlen($tag_id) == 0 || strlen($start) == 0 || strlen($limit) == 0) {
        return array();
    }
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }
    $query = "select t.id,t.name from tags t where t.id in (select id from (select pt.tag_id as id,count(pt.tag_id) as cnt from post_tags pt join post_tags pti on pti.post_id=pt.post_id where pti.tag_id=? and pt.tag_id<>? group by pt.tag_id order by cnt desc) a) limit ? , ?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("iiii", $tag_id, $tag_id, $start, $limit);
    $statement->bind_result($tid, $name);
    $statement->execute();
    $i = 0;
    $tags = array();
    while ($statement->fetch()) {
        $tags[$i] = array(
            "id" => $tid,
            "name" => $name
        );
        $i++;
    }
    if ($persistent_connection == null) {
        $db_connection->mysqli_connect_close();
    }
    return $tags;
}

function followTag($user_id, $tag_id, $persistent_connection) {
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }
    $query = "update tags set followers=followers+1 where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $tag_id);
    $statement->execute();

    $tag_follower = new Tag_follower();
    $tag_followercon = new Tag_followerController();
    $tag_follower->setTag_id($tag_id);
    $tag_follower->setUser_id($user_id);
    $follow_id = $tag_followercon->insert($tag_follower, $persistent_connection);

    if ($persistent_connection == null) {
        $db_connection->mysqli_connect_close();
    }
    return $follow_id;
}

function unfollowTag($follow_id, $persistent_connection) {
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }

    $tag_followercon = new Tag_followerController();
    $tag_follower = $tag_followercon->getByPrimaryKey($follow_id, array("tag_id"), null, $persistent_connection);

    $query = "update tags set followers=followers-1 where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $tag_follower->getTag_id());
    $statement->execute();

    $tag_followercon->delete($follow_id, $persistent_connection);

    if ($persistent_connection == null) {
        $db_connection->mysqli_connect_close();
    }
    return 1;
}

function convertTag($text) {
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES, "utf-8");
    $text = preg_replace("/[^a-zA-Z 0-9]+/", " ", $text);
    $text = mb_strtoupper($text, "utf-8");
    mb_regex_encoding("utf-8");
    $words = mb_split(' +', $text);
    if (sizeof($words) > 0) {
        require_once "../req/PorterStemmer.php";

        $stopWords = array("need", "thats", "using", "said", "he'd", "noted", "particular", "until", "becoming", "thanks", "over", "ff", "thereby", "she", "something", "right", "are", "these", "else", "once", "possibly", "shown", "respectively", "he", "theirs", "apart", "shows", "few", "further", "he's", "somebody", "herself", "downwards", "each", "wherever", "go", "t's", "she's", "before", "made", "accordingly", "indicate", "namely", "ed", "six", "hereafter", "sufficiently", "slightly", "she'd", "could", "consider", "eighty", "usually", "ninety", "tell", "do", "affecting", "whither", "f", "thorough", "look", "g", "ex", "d", "may", "e", "b", "noone", "c", "needs", "n", "o", "i", "l", "m", "j", "ones", "won't", "k", "h", "i", "yes", "somethan", "ref", "eg", "new", "s", "r", "what", "q", "p", "nothing", "having", "et", "strongly", "yet", "here's", "shes", "ca", "thru", "anywhere", "least", "took", "you'd", "by", "same", "enough", "has", "who", "couldn't", "would", "approximately", "any", "overall", "everybody", "had", "primarily", "be", "biol", "think", "get", "seeing", "begins", "likely", "far", "a's", "much", "and", "particularly", "co", "gotten", "near", "i'd", "often", "better", "against", "doing", "containing", "seeming", "of", "example", "i'm", "make", "invention", "does", "obtained", "saying", "shan't", "ignored", "aren", "tried", "former", "through", "possible", "following", "especially", "name", "tries", "edu", "all", "keeps", "five", "obviously", "makes", "she'll", "at", "as", "still", "an", "therefore", "hello", "neither", "shed", "never", "which", "ran", "see", "sec", "take", "am", "i'll", "anyone", "there", "an", "off", "nay", "ah", "thoroughly", "why", "they", "nobody", "somehow", "you've", "no", "nine", "otherwise", "hed", "anyways", "be", "of", "help", "given", "among", "recently", "hes", "says", "on", "only", "anybody", "ok", "her", "everyone", "that's", "itself", "oh", "maybe", "or", "done", "regarding", "third", "sensible", "them", "then", "will", "ought", "furthermore", "auth", "novel", "upon", "different", "indeed", "home", "getting", "announce", "most", "thanx", "across", "aside", "followed", "looking", "thank", "normally", "unless", "where's", "mg", "rather", "me", "ml", "aren't", "similar", "kept", "mr", "pages", "don't", "it's", "et-al", "my", "whereupon", "na", "okay", "specified", "per", "it'd", "how's", "nd", "sometime", "within", "thereupon", "described", "truly", "follows", "you're", "cause", "hid", "tends", "second", "last", "sometimes", "what", "being", "contains", "since", "actually", "him", "where", "every", "eight", "related", "potentially", "almost", "unto", "looks", "kg", "more", "results", "his", "inc", "we'd", "someone", "when", "wonder", "value", "useful", "none", "certainly", "seriously", "everywhere", "onto", "asking", "isn't", "appropriate", "such", "c's", "hers", "liked", "means", "whereafter", "here", "anymore", "heres", "predominantly", "km", "whole", "this", "causes", "appreciate", "becomes", "way", "adj", "about", "from", "hi", "believe", "while", "was", "id", "in", "allows", "ain't", "able", "if", "seemed", "corresponding", "ie", "below", "various", "wherein", "lest", "between", "less", "those", "is", "it", "added", "ourselves", "besides", "gives", "similarly", "important", "your", "gets", "into", "howbeit", "past", "im", "in", "know", "section", "two", "away", "necessary", "proud", "act", "themselves", "lets", "also", "found", "couldnt", "changes", "appear", "etc", "arent", "they'll", "ours", "hopefully", "its", "omitted", "yourselves", "showed", "exactly", "although", "c'mon", "formerly", "greetings", "it'll", "along", "entirely", "secondly", "serious", "alone", "awfully", "nowhere", "going", "relatively", "how", "under", "suggest", "available", "became", "always", "indicated", "theres", "inward", "refs", "itd", "own", "specify", "indicates", "try", "we", "reasonably", "give", "specifying", "i've", "accordance", "next", "use", "hardly", "vs", "run", "date", "consequently", "mrs", "resulting", "significant", "when's", "substantially", "whenever", "best", "mostly", "definitely", "unfortunately", "whatever", "we'll", "later", "back", "come", "us", "seen", "un", "seem", "cannot", "up", "gave", "either", "insofar", "sorry", "doesn't", "they'd", "down", "part", "quickly", "happens", "keep", "arise", "to", "affects", "com", "both", "inner", "become", "you'll", "to", "somewhere", "poorly", "meantime", "must", "th", "didn't", "necessarily", "mug", "affected", "after", "nevertheless", "whereby", "who's", "considering", "taken", "welcome", "is", "what's", "index", "however", "whose", "so", "behind", "gone", "willing", "whereas", "that", "associated", "than", "previously", "due", "unlikely", "thence", "several", "whom", "got", "ltd", "immediately", "hereby", "sub", "can", "www", "about", "well", "sup", "re", "rd", "above", "que", "qv", "four", "placed", "too", "the", "yours", "thus", "resulted", "moreover", "provides", "you", "soon", "owing", "who", "immediate", "seven", "anything", "effect", "whoever", "it", "abst", "certain", "pp", "somewhat", "our", "brief", "specifically", "very", "out", "forth", "via", "for", "hereupon", "hundred", "everything", "at", "towards", "zero", "whether", "went", "beyond", "elsewhere", "course", "whence", "are", "page", "shouldn't", "can't", "briefly", "yourself", "was", "therein", "thereafter", "information", "plus", "million", "others", "we're", "mainly", "viz", "did", "again", "wasn't", "like", "without", "'ll", "shall", "non", "not", "many", "present", "nos", "obtain", "or", "he'll", "nor", "haven't", "anyhow", "now", "cant", "promptly", "say", "myself", "saw", "ask", "some", "outside", "why's", "might", "put", "ord", "line", "self", "trying", "according", "they've", "seems", "will", "twice", "latter", "presumably", "probably", "inasmuch", "giving", "end", "want", "regardless", "hence", "just", "readily", "fifth", "let", "already", "research", "should", "as", "wouldn't", "really", "a", "successfully", "beforehand", "mustn't", "clearly", "despite", "hither", "old", "but", "afterwards", "on", "meanwhile", "wish", "herein", "hadn't", "amongst", "little", "show", "used", "together", "though", "been", "hasn't", "anyway", "sent", "were", "please", "toward", "there's", "three", "for", "concerning", "sure", "throughout", "except", "goes", "regards", "by", "we've", "comes", "wants", "himself", "knows", "importance", "contain", "even", "latterly", "known", "perhaps", "ever", "stop", "other", "allow", "have", "one", "selves", "com", "currently", "recent", "merely", "showns", "let's", "because", "another", "fix", "during", "lately", "mean", "beginnings", "apparently", "they're", "weren't", "with", "beginning", "nearly", "the", "came", "ending", "begin", "around", "beside", "nonetheless", "quite", "largely", "instead", "uses", "significantly", "their", "first", "miss");
        $adverbs = array("accidentally", "angrily", "anxiously", "awkwardly", "badly", "beautifully", "blindly", "boldly", "bravely", "brightly", "busily", "calmly", "carefully", "carelessly", "cautiously", "cheerfully", "clearly", "closely", "correctly", "courageously", "cruelly", "daringly", "deliberately", "doubtfully", "eagerly", "easily", "elegantly", "enormously", "enthusiastically", "equally", "eventually", "exactly", "faithfully", "fast", "fatally", "fiercely", "fondly", "foolishly", "fortunately", "frankly", "frantically", "generously", "gently", "gladly", "gracefully", "greedily", "happily", "hard", "hastily", "healthily", "honestly", "hungrily", "hurriedly", "inadequately", "ingeniously", "innocently", "inquisitively", "irritably", "joyously", "justly", "kindly", "lazily", "loosely", "loudly", "madly", "mortally", "mysteriously", "neatly", "nervously", "noisily", "obediently", "openly", "painfully", "patiently", "perfectly", "politely", "poorly", "powerfully", "promptly", "punctually", "quickly", "quietly", "rapidly", "rarely", "really", "recklessly", "regularly", "reluctantly", "repeatedly", "rightfully", "roughly", "rudely", "sadly", "safely", "selfishly", "sensibly", "seriously", "sharply", "shyly", "silently", "sleepily", "slowly", "smoothly", "so", "softly", "solemnly", "speedily", "stealthily", "sternly", "straight", "stupidly", "successfully", "suddenly", "suspiciously", "swiftly", "tenderly", "tensely", "thoughtfully", "tightly", "truthfully", "unexpectedly", "victoriously", "violently", "vivaciously", "warmly", "weakly", "wearily", "well", "wildly", "wisely");
        $adjectives = array("awesome", "different", "used", "important", "every", "large", "available", "popular", "able", "basic", "known", "various", "difficult", "several", "united", "historical", "hot", "useful", "mental", "scared", "additional", "emotional", "old", "political", "similar", "healthy", "financial", "medical", "traditional", "federal", "entire", "strong", "actual", "significant", "successful", "electrical", "expensive", "pregnant", "intelligent", "interesting", "poor", "happy", "responsible", "cute", "helpful", "recent", "willing", "nice", "wonderful", "impossible", "serious", "huge", "rare", "technical", "typical", "competitive", "critical", "electronic", "immediate", "whose", "aware", "educational", "environmental", "global", "legal", "relevant", "accurate", "capable", "dangerous", "dramatic", "efficient", "powerful", "foreign", "hungry", "practical", "psychological", "severe", "suitable", "numerous", "sufficient", "unusual", "consistent", "cultural", "existing", "famous", "pure", "afraid", "obvious", "careful", "latter", "obviously", "unhappy", "acceptable", "aggressive", "distinct", "eastern", "logical", "reasonable", "strict", "successfully", "administrative", "automatic", "civil", "former", "massive", "southern", "unfair", "visible", "alive", "angry", "desperate", "exciting", "friendly", "lucky", "realistic", "sorry", "ugly", "unlikely", "anxious", "comprehensive", "curious", "impressive", "informal", "inner", "pleasant", "sexual", "sudden", "terrible", "unable", "weak", "wooden", "asleep", "confident", "conscious", "decent", "embarrassed", "guilty", "lonely", "mad", "nervous", "odd", "remarkable", "substantial", "suspicious", "tall", "tiny", "more", "some", "one", "all", "many", "most", "other", "such", "even", "new", "just", "good", "any", "each", "much", "own", "great", "another", "same", "few", "free", "right", "still", "best", "public", "human", "both", "local", "sure", "better", "general", "specific", "enough", "long", "small", "less", "high", "certain", "little", "common", "next", "simple", "hard", "past", "big", "possible", "particular", "real", "major", "personal", "current", "left", "national", "least", "natural", "physical", "short", "last", "single", "individual", "main", "potential", "professional", "international", "lower", "open", "according", "alternative", "special", "working", "true", "whole", "clear", "dry", "easy", "cold", "commercial", "full", "low", "primary", "worth", "necessary", "positive", "present", "close", "creative", "green", "late", "fit", "glad", "proper", "complex", "content", "due", "effective", "middle", "regular", "fast", "independent", "original", "wide", "beautiful", "complete", "active", "negative", "safe", "visual", "wrong", "ago", "quick", "ready", "straight", "white", "direct", "excellent", "extra", "junior", "pretty", "unique", "classic", "final", "overall", "private", "separate", "western", "alone", "familiar", "official", "perfect", "bright", "broad", "comfortable", "flat", "rich", "warm", "young", "heavy", "valuable", "correct", "leading", "slow", "clean", "fresh", "normal", "secret", "tough", "brown", "cheap", "deep", "objective", "secure", "thin", "chemical", "cool", "extreme", "exact", "fair", "fine", "formal", "opposite", "remote", "total", "vast", "lost", "smooth", "dark", "double", "equal", "firm", "frequent", "internal", "sensitive", "constant", "minor", "previous", "raw", "soft", "solid", "weird", "amazing", "annual", "busy", "dead", "false", "round", "sharp", "thick", "wise", "equivalent", "initial", "narrow", "nearby", "proud", "spiritual", "wild", "adult", "apart", "brief", "crazy", "prior", "rough", "sad", "sick", "strange", "external", "illegal", "loud", "mobile", "nasty", "ordinary", "royal", "senior", "super", "tight", "upper", "yellow", "dependent", "funny", "gross", "ill", "spare", "sweet", "upstairs", "usual", "brave", "calm", "dirty", "downtown", "grand", "honest", "loose", "male", "quiet", "brilliant", "dear", "drunk", "empty", "female", "inevitable", "neat", "ok", "representative", "silly", "slight", "smart", "stupid", "temporary", "weekly", "that", "this", "what", "which", "time", "these", "work", "no", "only", "first", "over", "business", "his", "game", "think", "after", "life", "day", "home", "economy", "away", "either", "fat", "key", "training", "top", "level", "far", "fun", "house", "kind", "future", "action", "live", "period", "subject", "mean", "stock", "chance", "beginning", "upset", "chicken", "head", "material", "salt", "car", "appropriate", "inside", "outside", "standard", "medium", "choice", "north", "square", "born", "capital", "shot", "front", "living", "plastic", "express", "mood", "feeling", "otherwise", "plus", "saving", "animal", "budget", "minute", "character", "maximum", "novel", "plenty", "select", "background", "forward", "glass", "joint", "master", "red", "vegetable", "ideal", "kitchen", "mother", "party", "relative", "signal", "street", "connect", "minimum", "sea", "south", "status", "daughter", "hour", "trick", "afternoon", "gold", "mission", "agent", "corner", "east", "neither", "parking", "routine", "swimming", "winter", "airline", "designer", "dress", "emergency", "evening", "extension", "holiday", "horror", "mountain", "patient", "proof", "west", "wine", "expert", "native", "opening", "silver", "waste", "plane", "leather", "purple", "specialist", "bitter", "incident", "motor", "pretend", "prize", "resident");
        $pronouns = array("all", "another", "any", "anybody", "anyone", "anything", "both", "each", "each other", "either", "everybody", "everyone", "everything", "few", "he", "her", "hers", "herself", "him", "himself", "his", "i", "it", "its", "itself", "little", "many", "me", "mine", "more", "most", "much", "my", "myself", "neither", "no one", "nobody", "none", "nothing", "one", "one another", "other", "others", "our", "ours", "ourselves", "several", "she", "some", "somebody", "someone", "something", "that", "their", "theirs", "them", "themselves", "these", "they", "this", "those", "us", "we", "what", "whatever", "which", "whichever", "who", "whoever", "whom", "whomever", "whose", "you", "your", "yours", "yourself", "yourselves");
        $prepositions = array("aboard", "about", "above", "across", "after", "against", "along", "amid", "among", "anti", "around", "as", "at", "before", "behind", "below", "beneath", "beside", "besides", "between", "beyond", "but", "by", "concerning", "considering", "despite", "down", "during", "except", "excepting", "excluding", "following", "for", "from", "in", "inside", "into", "like", "minus", "near", "of", "off", "on", "onto", "opposite", "outside", "over", "past", "per", "plus", "regarding", "round", "save", "since", "than", "through", "to", "toward", "towards", "under", "underneath", "unlike", "until", "up", "upon", "versus", "via", "with", "within", "without");
        $conjunctions = array("and", "but", "or", "nor", "for ", "yet", "so", "after", "although", "as", "as if", "as long as", "as much as", "as soon as", "as though", "because", "before", "even", "even if", "even though", "if", "if only", "if when", "if then ", "inasmuch", "in order that", "just as", "lest", "now", "now since", "now that", "now when", "once", "provided", "provided that", "rather than", "since", "so that", "supposing", "than", "that", "though", "til", "unless", "until", "when", "whenever", "where", "whereas", "where if", "wherever", "whether", "which", "while", "who", "whoever", "why", "both", "either", "neither", "not only", "whether", "asleepsuch", "when", "no sooner", "rather", "then", "than");
        $interjections = array("aah", "absolutely", "achoo", "ack", "adios", "aha", "ahem", "ahoy", "agreed", "alack", "alright", "alrighty", "alrighty-roo", "alack", "alleluia", "all hail", "aloha", "amen", "anytime", "argh", "anyhoo", "anyhow", "as if", "attaboy", "attagirl", "awww", "awful", "ay", "bam", "bah hambug", "begorra", "behold", "bingo", "blah", "boo", "bravo", "brrr", "bye", "cheers", "ciao", "cripes", "crud", "darn", "dear", "doh", "drat", "eek", "eh", "encore", "eureka", "eww", "fiddlesticks", "fie", "gadzooks", "gee", "geepers", "gee whiz", "golly", "goodbye", "goodness", "goodness gracious", "gosh", "great", "ha", "ha-ha", "hail", "hallelujah", "heigh-ho", "hello", "hey", "hi", "hmm", "hmmm", "holy cow", "holy smokes", "hotdog", "huh", "hurray", "hush", "indeed", "jeepers creepers", "jeez", "lo and behold", "man", "my word", "no", "now", "nah", "oh", "oh my", "oh well", "ooh", "oops", "ouch", "ow", "phew", "phooey", "please", "pooh", "pow", "presto", "pshaw", "rats", "right-o", "scat", "shh", "shoo", "shucks", "so", "so long", "thanks", "there", "touché", "ugh", "uh-huh", "uh-oh", "ugh", "viva", "voila", "waa", "wahoo", "well", "whoopee", "whoops", "whoosh", "woah", "wow", "yay", "yea", "yeah", "yes", "yikes", "yippee", "yo", "yuck", "yummy");
        $verbs = array("accept", "add", "admire", "admit", "advise", "afford", "agree", "alert", "allow", "amuse", "analyse", "announce", "annoy", "answer", "apologise", "appear", "applaud", "appreciate", "approve", "argue", "arrange", "arrest", "arrive", "ask", "attach", "attack", "attempt", "attend", "attract", "avoid", "back", "bake", "balance", "ban", "bang", "bare", "bat", "bathe", "battle", "beam", "beg", "behave", "belong", "bleach", "bless", "blind", "blink", "blot", "blush", "boast", "boil", "bolt", "bomb", "book", "bore", "borrow", "bounce", "bow", "box", "brake", "branch", "breathe", "bruise", "brush", "bubble", "bump", "burn", "bury", "buzz", "calculate", "call", "camp", "care", "carry", "carve", "cause", "challenge", "change", "charge", "chase", "cheat", "check", "cheer", "chew", "choke", "chop", "claim", "clap", "clean", "clear", "clip", "close", "coach", "coil", "collect", "colour", "comb", "command", "communicate", "compare", "compete", "complain", "complete", "concentrate", "concern", "confess", "confuse", "connect", "consider", "consist", "contain", "continue", "copy", "correct", "cough", "count", "cover", "crack", "crash", "crawl", "cross", "crush", "cry", "cure", "curl", "curve", "cycle", "dam", "damage", "dance", "dare", "decay", "deceive", "decide", "decorate", "delay", "delight", "deliver", "depend", "describe", "desert", "deserve", "destroy", "detect", "develop", "disagree", "disappear", "disapprove", "disarm", "discover", "dislike", "divide", "double", "doubt", "drag", "drain", "dream", "dress", "drip", "drop", "drown", "drum", "dry", "dust", "earn", "educate", "embarrass", "employ", "empty", "encourage", "end", "enjoy", "enter", "entertain", "escape", "examine", "excite", "excuse", "exercise", "exist", "expand", "expect", "explain", "explode", "extend", "face", "fade", "fail", "fancy", "fasten", "fax", "fear", "fence", "fetch", "file", "fill", "film", "fire", "fit", "fix", "flap", "flash", "float", "flood", "flow", "flower", "fold", "follow", "fool", "force", "form", "found", "frame", "frighten", "fry", "gather", "gaze", "glow", "glue", "grab", "grate", "grease", "greet", "grin", "grip", "groan", "guarantee", "guard", "guess", "guide", "hammer", "hand", "handle", "hang", "happen", "harass", "harm", "hate", "haunt", "head", "heal", "heap", "heat", "help", "hit", "hook", "hop", "hope", "hover", "hug", "hum", "hunt", "hurry", "identify", "ignore", "imagine", "impress", "improve", "include", "increase", "influence", "inform", "inject", "injure", "instruct", "intend", "interest", "interfere", "interrupt", "introduce", "invent", "invite", "irritate", "itch", "jail", "jam", "jog", "join", "joke", "judge", "juggle", "jump", "kick", "kill", "kiss", "kneel", "knit", "knock", "knot", "label", "land", "last", "laugh", "launch", "learn", "level", "license", "lick", "lie", "lighten", "like", "list", "listen", "live", "load", "lock", "long", "look", "love", "man", "manage", "march", "mark", "marry", "match", "mate", "matter", "measure", "meddle", "melt", "memorise", "mend", "mess up", "milk", "mine", "miss", "mix", "moan", "moor", "mourn", "move", "muddle", "mug", "multiply", "murder", "nail", "name", "need", "nest", "nod", "note", "notice", "number", "obey", "object", "observe", "obtain", "occur", "offend", "offer", "open", "order", "overflow", "owe", "own", "pack", "paddle", "paint", "park", "part", "pass", "paste", "pat", "pause", "peck", "pedal", "peel", "peep", "perform", "permit", "phone", "pick", "pinch", "pine", "place", "plan", "plant", "play", "please", "plug", "point", "poke", "polish", "pop", "possess", "post", "pour", "practise", "pray", "preach", "precede", "prefer", "prepare", "present", "preserve", "press", "pretend", "prevent", "prick", "print", "produce", "program", "promise", "protect", "provide", "pull", "pump", "punch", "puncture", "punish", "push", "question", "queue", "race", "radiate", "rain", "raise", "reach", "realise", "receive", "recognise", "record", "reduce", "reflect", "refuse", "regret", "reign", "reject", "rejoice", "relax", "release", "rely", "remain", "remember", "remind", "remove", "repair", "repeat", "replace", "reply", "report", "reproduce", "request", "rescue", "retire", "return", "rhyme", "rinse", "risk", "rob", "rock", "roll", "rot", "rub", "ruin", "rule", "rush", "sack", "sail", "satisfy", "save", "saw", "scare", "scatter", "scold", "scorch", "scrape", "scratch", "scream", "screw", "scribble", "scrub", "seal", "search", "separate", "serve", "settle", "shade", "share", "shave", "shelter", "shiver", "shock", "shop", "shrug", "sigh", "sign", "signal", "sin", "sip", "ski", "skip", "slap", "slip", "slow", "smash", "smell", "smile", "smoke", "snatch", "sneeze", "sniff", "snore", "snow", "soak", "soothe", "sound", "spare", "spark", "sparkle", "spell", "spill", "spoil", "spot", "spray", "sprout", "squash", "squeak", "squeal", "squeeze", "stain", "stamp", "stare", "start", "stay", "steer", "step", "stir", "stitch", "stop", "store", "strap", "strengthen", "stretch", "strip", "stroke", "stuff", "subtract", "succeed", "suck", "suffer", "suggest", "suit", "supply", "support", "suppose", "surprise", "surround", "suspect", "suspend", "switch", "talk", "tame", "tap", "taste", "tease", "telephone", "tempt", "terrify", "test", "thank", "thaw", "tick", "tickle", "tie", "time", "tip", "tire", "touch", "tour", "tow", "trace", "trade", "train", "transport", "trap", "travel", "treat", "tremble", "trick", "trip", "trot", "trouble", "trust", "try", "tug", "tumble", "turn", "twist", "type", "undress", "unfasten", "unite", "unlock", "unpack", "untidy", "use", "vanish", "visit", "wail", "wait", "walk", "wander", "want", "warm", "warn", "wash", "waste", "watch", "water", "wave", "weigh", "welcome", "whine", "whip", "whirl", "whisper", "whistle", "wink", "wipe", "wish", "wobble", "wonder", "work", "worry", "wrap", "wreck", "wrestle", "wriggle", "x-ray", "yawn", "yell", "zip", "zoom");
        $nouns = array("audience","ball", "bat", "bed", "book", "boy", "bun", "can", "cake", "cap", "car", "cat", "cow", "cub", "cup", "dad", "day", "dog", "doll", "dust", "fan", "feet", "girl", "gun", "hand", "hall", "hat", "hen", "jar", "kite", "man", "map", "men", "mom", "pan", "pet", "pie", "pig", "pot", "rat", "son", "sun", "toe", "tub", "van", "apple", "arm", "banana", "bike", "bird", "book", "chin", "clam", "class", "clover", "club", "corn", "crayon", "crow", "crown", "crowd", "crib", "desk", "dime", "dirt", "dress", "fang ", "field", "flag", "flower", "fog", "game", "heat", "hill", "home", "horn", "hose", "joke", "juice", "kite", "lake", "maid", "mask", "mice", "milk", "mint", "meal", "meat", "moon", "mother", "morning", "name", "nest", "nose", "pear", "pen", "pencil", "plant", "rain", "river", "road", "rock", "room", "rose", "seed", "shape", "shoe", "shop", "show", "sink", "snail", "snake", "snow", "soda", "sofa", "star", "step", "stew", "stove", "straw", "string", "summer", "swing", "table", "tank", "team", "tent", "test", "toes", "tree", "vest", "water", "wing", "winter", "woman", "women", "2nd grade", "", "alarm", "animal", "aunt", "bait", "balloon", "bath", "bead", "beam", "bean", "bedroom", "boot", "bread", "brick", "brother", "camp", "chicken", "children", "crook", "deer", "dock", "doctor", "downtown", "drum", "dust", "eye", "family", "father", "fight", "flesh", "food", "frog", "goose", "grade", "grandfather", "grandmother", "grape", "grass", "hook", "horse", "jail", "jam", "kiss", "kitten", "light", "loaf", "lock", "lunch", "lunchroom", "market", "meal", "mother", "notebook", "owl", "pail", "parent", "park", "plot", "product", "rabbit", "rake", "robin", "sack", "sail", "scale", "sea", "sister", "soap", "song", "spark", "space", "spoon", "spot", "spy", "summer", "tiger", "toad", "town", "trail", "tramp", "tray", "trick", "trip", "uncle", "vase", "winter", "water", "week", "wheel", "wish", "wool", "yard", "zebra", "actor", "airplane", "airport", "army", "baseball", "beef", "birthday", "boy", "brush", "bushes", "butter ", "cast", "cave", "cent", "cherries", "cherry", "cobweb", "coil", "cracker", "dinner", "eggnog", "elbow", "face", "fireman", "flavor", "gate", "glove", "glue", "goldfish", "goose", "grain", "hair", "haircut", "hobbies", "holiday", "hot", "jellyfish", "ladybug", "mailbox", "number", "oatmeal", "pail", "pancake", "pear", "pest", "popcorn", "queen", "quicksand", "quiet", "quilt", "rainstorm", "scarecrow", "scarf", "stream", "street", "sugar", "throne", "toothpaste", "twig", "volleyball", "wood", "wrench", "advice", "anger", "answer", "apple", "arithmetic", "badge", "basket", "basketball", "battle", "beast", "beetle", "beggar", "brain", "branch", "bubble", "bucket", "cactus", "cannon", "cattle", "celery", "cellar", "cloth", "coach", "coast", "crate", "cream", "daughter", "donkey", "drug", "earthquake", "feast", "fifth", "finger", "flock", "frame", "furniture", "geese", "ghost", "giraffe", "governor", "honey", "hope", "hydrant", "icicle", "income", "island", "jeans", "judge", "lace", "lamp", "lettuce", "marble", "month", "north", "ocean", "patch", "plane", "playground", "poison", "riddle", "rifle", "scale", "seashore", "sheet", "sidewalk", "skate", "slave", "sleet", "smoke", "stage", "station", "thrill", "throat", "throne", "title", "toothbrush", "turkey", "underwear", "vacation", "vegetable", "visitor", "voyage", "year", "able", "achieve", "acoustics", "action", "activity", "aftermath", "afternoon", "afterthought", "apparel", "appliance", "beginner", "believe", "bomb", "border", "boundary", "breakfast", "cabbage", "cable", "calculator", "calendar", "caption", "carpenter", "cemetery", "channel", "circle", "creator", "creature", "education", "faucet", "feather", "friction", "fruit", "fuel", "galley", "guide", "guitar", "health", "heart", "idea", "kitten", "laborer", "language", "lawyer", "linen", "locket", "lumber", "magic", "minister", "mitten", "money", "mountain", "music", "partner", "passenger", "pickle", "picture", "plantation", "plastic", "pleasure", "pocket", "police", "pollution", "product", "railway", "recess", "reward", "route", "scene", "scent", "squirrel", "stranger", "suit", "sweater", "temper", "territory", "texture", "thread", "treatment", "veil", "vein", "volcano", "wealth", "weather", "wilderness", "wren", "wrist", "writer");

        foreach ($words as $key => $word) {
            $words[$key] = PorterStemmer::Stem(strtolower($word), true);
        }
        foreach ($stopWords as $key => $word)
            $stopWords[$key] = PorterStemmer::Stem(strtolower($word), true);

        $words = array_diff($words, $stopWords);
        $words = array_diff($words, $adverbs);
        $words = array_diff($words, $adjectives);
        $words = array_diff($words, $pronouns);
        $words = array_diff($words, $prepositions);
        $words = array_diff($words, $conjunctions);
        $words = array_diff($words, $interjections);
        $words = array_diff($words, $verbs);
        $words = array_diff($words, $nouns);
        $keywordCounts = array_count_values($words);
        arsort($keywordCounts, SORT_NUMERIC);
        return array_keys($keywordCounts);
    } else {
        return array();
    }
}

?>
