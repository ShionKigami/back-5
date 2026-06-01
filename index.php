// НАЧАЛО POST-блока (после else {)
else {
    $errors = FALSE;
    
    // НЕ СТАРТУЙТЕ СЕССИЮ СРАЗУ!
    // session_start(); // УБРАТЬ ОТСЮДА
    
    // ... вся валидация ...
    
    if ($errors) {
        header('Location: index.php');
        exit();
    }
    
    // СТАРТУЕМ СЕССИЮ ТОЛЬКО ЗДЕСЬ, ПОСЛЕ ВАЛИДАЦИИ
    session_start();
    
    $error_cookies = ['name', 'phone', 'email', 'birthdate', 'sex', 'languages', 'contract'];
    foreach ($error_cookies as $field) {
        setcookie($field . '_error', '', 100000);
    }
    
    $is_update = false;
    // Проверяем, авторизован ли пользователь
    if (!empty($_SESSION['login'])) {
        $is_update = true;
    }
    
    try {
        $db->beginTransaction();
        
        if ($is_update) {
            // ОБНОВЛЕНИЕ существующего пользователя
            $check_stmt = $db->prepare("SELECT id FROM users WHERE login = ?");
            $check_stmt->execute([$_SESSION['login']]);
            $existing_user = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing_user) {
                throw new Exception("Пользователь не найден");
            }
            
            $user_id = $existing_user['id'];
            
            $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, email = ?, birthdate = ?, sex = ?, biography = ? WHERE id = ?");
            $stmt->execute([
                $_POST['name'],
                $_POST['phone'] ?? null,
                $_POST['email'] ?? null,
                $_POST['birthdate'] ?? null,
                $_POST['sex'],
                $_POST['biography'] ?? null,
                $user_id
            ]);
            
            $del_stmt = $db->prepare("DELETE FROM user_languages WHERE user_id = ?");
            $del_stmt->execute([$user_id]);
            
        } else {
            // НОВЫЙ пользователь
            $login = 'user_' . rand(1000, 9999) . uniqid();
            $pass = substr(md5(uniqid(rand(), true)), 0, 8);
            $pass_hash = md5($pass);
            
            $stmt = $db->prepare("INSERT INTO users (name, phone, email, birthdate, sex, biography, login, pass_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $_POST['phone'] ?? null,
                $_POST['email'] ?? null,
                $_POST['birthdate'] ?? null,
                $_POST['sex'],
                $_POST['biography'] ?? null,
                $login,
                $pass_hash
            ]);
            
            $user_id = $db->lastInsertId();
            
            // Сохраняем в сессию данные нового пользователя
            $_SESSION['login'] = $login;
            $_SESSION['uid'] = $user_id;
        }
        
        // Вставляем языки
        $lang_stmt = $db->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
        foreach ($_POST['languages'] as $lang) {
            $lang_stmt->execute([$user_id, $lang]);
        }
        
        $db->commit();
        
        setcookie('save', '1', time() + 30 * 24 * 60 * 60);
        
        if (!$is_update) {
            setcookie('login', $login, time() + 30 * 24 * 60 * 60);
            setcookie('pass', $pass, time() + 30 * 24 * 60 * 60);
        }
        
        header('Location: index.php');
        exit();
        
    } catch (PDOException $e) {
        $db->rollBack();
        die('Ошибка базы данных: ' . $e->getMessage());
    } catch (Exception $e) {
        $db->rollBack();
        die('Ошибка: ' . $e->getMessage());
    }
}
