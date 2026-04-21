<?php
/**
 * Instans av klassen skapar en koppling till databasen egytalk
 * och tillhandahåller ett antal metoder för att hämta och manipulera data i databasen.
 * Metoder och metodnamn är bara förslag
 */
class DbEgyTalk
{
    /**
     * Används i metoder genom <code>$this->db</code>
     */
    private $db;

    /**
     * DbEgyTalk constructor.
     *
     * Skapar en koppling till databaseb egytalk
     */
    public function __construct()
    {
        // Definierar konstanter med användarinformation.
        define('DB_USER', 'root');
        define('DB_PASSWORD', '12345');
        define('DB_HOST', 'mariadb');
        define('DB_NAME', 'Social_app');

        // Skapar en anslutning till MySql och databasen world
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
        $this->db = new PDO($dsn, DB_USER, DB_PASSWORD);

        // Sätt PDO i error mode för att visa fel
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // skapa tabeller om de inte finns (enkelt migrationsalternativ)
        $this->db->exec("CREATE TABLE IF NOT EXISTS friends (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            friend_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_pair (user_id, friend_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->exec("CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_id INT NOT NULL,
            to_id INT NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    /**
     * Kontrollerar av användare och lösen.
     * Skapar global sessions-array med användarinformation.
     * Tidigare har metoden hetat getUser.
     *
     * @param  $userName  Användarnamn
     * @param  $passWord  Lösenord
     * @return $response  användardata eller tom [] om inloggning misslyckas
     */
    function auth($userName, $passWord): array
    {
        $userName = trim($userName);
        $response = [];

        /* Bygger upp sql frågan */
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([":username" => $userName]);

        /** Kontroll att resultat finns */
        if ($stmt->rowCount() == 1) {
            // Hämtar användaren, kan endast vara 1 person
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Kontrollerar lösenordet, och allt ok.
            if (password_verify($passWord, $user['password'])) {
                $response['uid'] = $user['id'];
                $response['username'] = $user['username'];
                $response['display_name'] = $user['display_name'];
                $response['email'] = $user['email'];
                $response['biography'] = $user['biography'] ?? '';

                // Parse display_name to extract firstname and surname
                $nameParts = explode(' ', trim($user['display_name']), 2);
                $response['firstname'] = $nameParts[0] ?? '';
                $response['surname'] = $nameParts[1] ?? '';
            }
        }

        return $response;
    }

    /**
     * Hämtar anvädardata från användare med secifikt användarID
     * 
     * @param  $uid      användarID
     * @return $response användardata eller tom [] om ingen anvädare hittats eller fel inträffat
     */
    function getUserFromUid($uid)
    {
        $response = [];

        // Egen kod!

        return $response;
    }

    /**
     * Hämtar alla poster som gjorts på egytalk
     *
     * @return array med alla poster
     */
    function getAllPosts()
    {
        $posts = [];

        try {
            $sqlkod = "SELECT post.*, users.firstname, users.surname FROM post 
                NATURAL JOIN users ORDER BY post.date DESC";
            $stmt = $this->db->prepare($sqlkod);
            $stmt->execute();

            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < count($posts); $i++) {
                $posts[$i]['comments'] = $this->getComments($posts[$i]['pid']);
            }
        } catch (Exception $e) {
        }

        return $posts;
    }

    /**
     * Hämtar poster för en användare,
     * sorterade efter publiceringsdatum
     *
     * @param   $uid     användar-ID för användaren
     * @return  array    med statusuppdateringar sorterade efter datum
     */
    function getPosts($uid)
    {
        $posts = [];

        // Egen kod!

        return $posts;
    }

    /**
     * Hämtar alla kommentarer till en post
     *
     * @param  $pid   postens ID-nummer
     * @return array  med kommentarer sorterade efter datum
     */
    function getComments($pid)
    {
        $comments = [];

        // Egen kod!

        return $comments;
    }

    /**
     * Skapar ny samtalstråd.
     *
     * @param  $uid       Användar-ID
     * @param  $postTxt   Postat inlägg
     * @return true       om det lyckades, annars false
     */
    function addPost($uid, $postTxt)
    {
        $postTxt = filter_var($postTxt, FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            $stmt = $this->db->prepare("INSERT INTO post(uid, post_txt, date) VALUES(:uid, :post, :date)");

            $stmt->bindValue(":uid", $uid);
            $stmt->bindValue(":post", $postTxt);
            $stmt->bindValue(":date", date("Y-m-d H:i:s"));

            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Lägger till en ny kommentar till en post.
     *
     * @param  $userID    Användar-ID för den som skriver kommentaren
     * @param  $statusID  Status-ID för statusuppdatering som kommenteras
     * @param  $comment   Kommentar
     * @return true om det lyckades, annars false
     */
    // function addComment($uid, $pid, $comment)
    // {
    //     $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
    //     $comment = filter_var($comment, FILTER_SANITIZE_SPECIAL_CHARS);

    //     try {

    //         // Egen kod!

    //         //return $stmt->execute();
    //     } catch (Exception $e) {
    //         return false;
    //     }
    // }

    /**
     * Kontrollerar om ett användarnamn redan finns
     *
     * @param  $user    Användarnamn
     * @return true om användarnamnet finns, annars false
     */
    function userExists($user)
    {
        $user = trim(filter_var($user, FILTER_UNSAFE_RAW));

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE username = :user");
            $stmt->bindValue(":user", $user);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    function isUsernameTakenByOther($username, $uid)
    {
        $username = trim(filter_var($username, FILTER_UNSAFE_RAW));
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE username = :username AND id != :uid");
            $stmt->bindValue(":username", $username);
            $stmt->bindValue(":uid", (int) $uid, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    function updateUsername($uid, $username)
    {
        $username = trim(filter_var($username, FILTER_UNSAFE_RAW));
        if ($username === '' || strlen($username) < 3 || strlen($username) > 30) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("SELECT username FROM users WHERE id = :uid");
            $stmt->bindValue(":uid", (int) $uid, PDO::PARAM_INT);
            $stmt->execute();
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                return false;
            }

            if ($existing['username'] === $username) {
                return true;
            }

            if ($this->isUsernameTakenByOther($username, $uid)) {
                return false;
            }

            $stmt = $this->db->prepare("UPDATE users SET username = :username WHERE id = :uid");
            $stmt->bindValue(":username", $username);
            $stmt->bindValue(":uid", (int) $uid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Lägger till en ny användare
     *
     * @param  $fname   Förnamn
     * @param  $sname   Efternamn
     * @param  $user    Användarnamn
     * @param  $email   Email
     * @param  $pwd     Lösenord
     * @return true om det lyckades, annars false
     */
    function addUser($fname, $sname, $user, $email, $pwd)
    {
        $fname = filter_var($fname, FILTER_SANITIZE_SPECIAL_CHARS);
        $sname = filter_var($sname, FILTER_SANITIZE_SPECIAL_CHARS);
        $user = filter_var($user, FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($email, FILTER_SANITIZE_SPECIAL_CHARS);
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);

        // Kombinera förnamn och efternamn till display_name
        $displayName = trim($fname . ' ' . $sname);

        try {
            $stmt = $this->db->prepare("INSERT INTO users(username, password, display_name, email) VALUES(:user, :pwd, :display, :email)");
            $stmt->bindValue(":user", $user);
            $stmt->bindValue(":email", $email);
            $stmt->bindValue(":pwd", $pwd);
            $stmt->bindValue(":display", $displayName);

            return $stmt->execute();
        } catch (Exception $e) {
            // Logga fel för debugging
            error_log("AddUser Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hämtar alla avändare i nätverket
     * @return array med användare
     */
    function getUsers()
    {
        $users = [];
        try {
            $sql = "SELECT id, display_name, username, biography AS bio FROM users ORDER BY display_name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error if needed
        }

        return $users;
    }

    /**
     * Söker efter användare.
     *
     * @param  $searchWord    Sökord
     * @return array med användare
     */
    function findUsers($searchWord)
    {
        $searchWord = filter_var($searchWord, FILTER_UNSAFE_RAW);
        // Ev mer om phone och mail är med i tabellen users
        // Sök efter display_name eller username och returnera samma fält som getUsers
        $sql = "SELECT id as uid, display_name, username, biography AS bio FROM users 
                WHERE display_name LIKE :search OR username LIKE :search 
                ORDER BY display_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":search", "%$searchWord%");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returnerar användarinstälningar
     * om usertabellen har  mail och phone
     *
     * @param  $uid      användarens uid
     * @return json-obj  med användardata, mail, phone
     */
    function getSettings($uid)
    {
        $settings = [];

        try {
            $stmt = $this->db->prepare("SELECT mail, phone FROM users WHERE uid = :uid");
            $stmt->bindValue(":uid", $uid);

            if ($stmt->execute())
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        }

        return $settings;
    }

    /**
     * Uppdaterar användarinstälningar
     *
     * @param  $uid      användarens uid
     * @param  $settings array med inställningar, $settings['phone'], $settings['mail']
     * @return true      om uppdateringen lyckades
     */
    function setSettings($uid, $settings)
    {
        $success = false;

        // Egen kod!

        return $success;
    }

    /**
     * Uppdaterar användarens biography-fält
     *
     * @param int $uid
     * @param string $bio
     * @return bool true om någon rad uppdaterades
     */
    function updateBiography($uid, $bio)
    {
        try {
            $stmt = $this->db->prepare("UPDATE users SET biography = :bio WHERE id = :uid");
            $stmt->bindValue(":bio", $bio);
            $stmt->bindValue(":uid", $uid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Lägg till en vänrelation
     * @param int $userId
     * @param int $friendId
     * @return bool
     */
    function addFriend($userId, $friendId)
    {
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO friends (user_id, friend_id) VALUES (:uid, :fid)");
            $stmt->bindValue(":uid", $userId, PDO::PARAM_INT);
            $stmt->bindValue(":fid", $friendId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Hämta lista på vänner för en användare
     * @param int $userId
     * @return array
     */
    function getFriends($userId)
    {
        $friends = [];
        try {
            $sql = "SELECT u.id, u.display_name, u.username, u.biography AS bio
                    FROM users u
                    JOIN friends f ON f.friend_id = u.id
                    WHERE f.user_id = :uid
                    ORDER BY u.display_name";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":uid", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        }
        return $friends;
    }

    /**
     * Skapa ett meddelande i chatten
     * @param int $fromId
     * @param int $toId
     * @param string $text
     * @return bool
     */
    function addMessage($fromId, $toId, $text)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO messages (from_id, to_id, message) VALUES (:from, :to, :msg)");
            $stmt->bindValue(":from", $fromId, PDO::PARAM_INT);
            $stmt->bindValue(":to", $toId, PDO::PARAM_INT);
            $stmt->bindValue(":msg", $text);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Hämta samtalslistan (en post per vän med senaste meddelande)
     * @param int $userId
     * @return array
     */
    function getConversations($userId)
    {
        $convs = [];
        try {
            $sql = "SELECT
                        CASE
                            WHEN m.from_id = :uid THEN m.to_id
                            ELSE m.from_id
                        END AS other_id,
                        u.display_name,
                        u.username,
                        u.biography AS bio,
                        m.message,
                        m.created_at
                    FROM messages m
                    JOIN users u ON u.id = (CASE WHEN m.from_id = :uid THEN m.to_id ELSE m.from_id END)
                    WHERE m.from_id = :uid OR m.to_id = :uid
                    ORDER BY m.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":uid", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $convs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        }
        return $convs;
    }

    /**
     * Hämta meddelanden mellan två användare
     * @param int $uid
     * @param int $otherId
     * @return array
     */
    function getMessagesBetween($uid, $otherId)
    {
        $msgs = [];
        try {
            $sql = "SELECT from_id, to_id, message, created_at
                    FROM messages
                    WHERE (from_id = :uid AND to_id = :other)
                       OR (from_id = :other AND to_id = :uid)
                    ORDER BY created_at ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":uid", $uid, PDO::PARAM_INT);
            $stmt->bindValue(":other", $otherId, PDO::PARAM_INT);
            $stmt->execute();
            $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        }
        return $msgs;
    }

    /**
     * Verifierar om lösenord överenstämmer med användarens lösenord
     *
     * @param $uid    Användarens uid 
     * @param $pwd    Lösenord som skall testas
     * @return true   om löseordet är korrekt
     */
    private function verifyPassword($uid, $pwd)
    {
        $verified = false;

        try {
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :uid ");
            $stmt->bindValue(":uid", $uid, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $verified = password_verify($pwd, $user['password']);
            }

        } catch (Exception $e) {
        }

        return $verified;
    }

    /**
     * Uppdaterar lösenorder
     *
     * @param  $uid      användarens uid
     * @param  $oldpwd   Nuvarande lösenord
     * @param  $pwd      Nytt lösenord
     * @return true om uppdateringen lyckades
     */
    function setPassword($uid, $oldpwd, $pwd)
    {
        $success = false;
        if ($this->verifyPassword($uid, $oldpwd)) {

            try {

                $newHash = password_hash($pwd, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("UPDATE users SET password = :pwd WHERE id = :uid");
                $stmt->bindValue(":pwd", $newHash);
                $stmt->bindValue(":uid", $uid, PDO::PARAM_INT);
                $stmt->execute();
                $success = $stmt->rowCount() > 0;
            } catch (Exception $e) {
            }
        }

        return $success;
    }

    function updateProfilePicture($uid, $path)
    {
        $stmt = $this->db->prepare("UPDATE users SET pfps = :path WHERE id = :uid");
        $stmt->execute([':path' => $path, ':uid' => (int) $uid]);
        return $stmt->rowCount() > 0;
    }

    function getProfilePicture($uid)
    {
        $stmt = $this->db->prepare("SELECT pfps FROM users WHERE id = :uid");
        $stmt->execute([':uid' => (int) $uid]);
        return $stmt->fetchColumn() ?: '/assets/default-pfp.png';
    }

    /**
     * Hämtar alla trådar (posts) från databasen
     * @return array Lista med trådar (assoc arrays)
     */
    public function getAllThreads()
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                t.id AS thread_id,
                t.user_id,
                t.content,
                t.created_at,
                u.username,
                u.display_name,
                (SELECT COUNT(*) FROM comments c WHERE c.thread_id = t.id) AS comment_count
            FROM threads t
            LEFT JOIN users u ON t.user_id = u.id
            ORDER BY t.created_at DESC
            LIMIT 50
        ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getAllThreads error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lägger till en ny tråd (post)
     */
    public function addThread($user_id, $content)
    {
        try {
            $stmt = $this->db->prepare("
            INSERT INTO threads (user_id, content, created_at)
            VALUES (:uid, :content, NOW())
        ");
            $stmt->execute([
                ':uid' => (int) $user_id,
                ':content' => $content
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("addThread PDO error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("addThread general error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hämtar alla kommentarer för en given tråd
     */
    public function getCommentsForThread($thread_id)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                c.id,
                c.content,
                c.created_at,
                u.username,
                u.display_name
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.thread_id = :tid
            ORDER BY c.created_at ASC
        ");
            $stmt->execute([':tid' => (int) $thread_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getCommentsForThread error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lägger till en kommentar
     */
    public function addComment($thread_id, $user_id, $content)
    {
        try {
            $stmt = $this->db->prepare("
            INSERT INTO comments (thread_id, user_id, content, created_at)
            VALUES (:tid, :uid, :content, NOW())
        ");
            $stmt->execute([
                ':tid' => (int) $thread_id,
                ':uid' => (int) $user_id,
                ':content' => $content
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("addComment error: " . $e->getMessage());
            return false;
        }
    }








}