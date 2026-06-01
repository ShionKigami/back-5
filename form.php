<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма </title>
    <style>
        .error {
            border: 2px solid red;
        }
        #messages .error {
            border: none;
            color: red;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    
<?php if (!empty($_SESSION['login'])): ?>
    <div style="background: #f0f0f0; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <p>Вы авторизованы как: <strong><?php echo htmlspecialchars($_SESSION['login']); ?></strong></p>
        <form method="POST" action="logout.php" style="display: inline;">
            <button type="submit" style="background: #dc3545; color: white; border: none; padding: 5px 15px; border-radius: 3px; cursor: pointer;">
                Выйти
            </button>
        </form>
        <p style="font-size: 12px; color: #666; margin-top: 10px;">
            Выход из системы не удалит ваши данные. Вы сможете войти снова с логином и паролем, которые получили при регистрации.
        </p>
    </div>
<?php endif; ?>

<?php
if (!empty($messages)) {
    print('<div id="messages">');
    foreach ($messages as $message) {
        print($message);
    }
    print('</div>');
}
?>

<form action="" method="POST">
    <label>ФИО:</label>
    <input type="text" name="name" <?php if ($errors['name']) print 'class="error"'; ?> value="<?php echo htmlspecialchars($values['name'] ?? ''); ?>" required /><br/>

    <label>Телефон:</label>
    <input type="tel" name="phone" <?php if ($errors['phone']) print 'class="error"'; ?> value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>" /><br/>

    <label>E-mail:</label>
    <input type="email" name="email" <?php if ($errors['email']) print 'class="error"'; ?> value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>" /><br/>

    <label>Дата рождения:</label>
    <input type="date" name="birthdate" <?php if ($errors['birthdate']) print 'class="error"'; ?> value="<?php echo htmlspecialchars($values['birthdate'] ?? ''); ?>" /><br/>

    <label>Пол:</label>
    <input type="radio" name="sex" <?php if ($errors['sex']) print 'class="error"'; ?> value="male" <?php echo (isset($values['sex']) && $values['sex'] == 'male') ? 'checked' : ''; ?> /> Мужской
    <input type="radio" name="sex" <?php if ($errors['sex']) print 'class="error"'; ?> value="female" <?php echo (isset($values['sex']) && $values['sex'] == 'female') ? 'checked' : ''; ?> /> Женский<br/>

    <fieldset <?php if ($errors['languages']) print 'class="error"'; ?>>
        <legend>Любимый язык программирования:</legend>
        <?php
        $all_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
        $selected_langs = $values['languages'] ?? [];
        if (!is_array($selected_langs)) {
            $selected_langs = [];
        }
        foreach ($all_languages as $lang) {
            $checked = in_array($lang, $selected_langs) ? 'checked' : '';
            echo "<input type='checkbox' name='languages[]' value='" . htmlspecialchars($lang) . "' $checked /> " . htmlspecialchars($lang) . "<br/>";
        }
        ?>
    </fieldset>

    <label>Биография:</label><br/>
    <textarea name="biography" rows="5" cols="40"><?php echo htmlspecialchars($values['biography'] ?? ''); ?></textarea><br/>

    <label>
        <input type="checkbox" name="contract" value="1" <?php if ($errors['contract']) print 'class="error"'; ?> <?php echo (isset($values['contract']) && $values['contract'] == '1') ? 'checked' : ''; ?> />
        Ознакомлен
    </label><br/>

    <input type="submit" value="Сохранить" />
</form>

</body>
</html>
