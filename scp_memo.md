私がEC-CUBEから変更箇所を引っ張ってきた時のメモ

コピー元、コピー先を変えたら使えるかもしれないですが、
既存ファイルも中身が書き変わってしまいますので、内容わかってる人のみ参考にしてください。

```bash
scp aws:/var/www/eccube/app/config/eccube/bundles.php ./app/config/eccube
scp aws:/var/www/eccube/app/config/eccube/firebase_credentials.json ./app/config/eccube
scp aws:/var/www/eccube/app/config/eccube/packages/knpu_oauth2_client.yaml ./app/config/eccube/packages
scp aws:/var/www/eccube/app/config/eccube/packages/firebase.yaml ./app/config/eccube/packages
scp aws:/var/www/eccube/app/Customize/Controller/* ./app/Customize/Controller
scp aws:/var/www/eccube/app/Customize/Entity/CustomerTrait.php ./app/Customize/Entity
scp aws:/var/www/eccube/app/Customize/Form/Extension/EntryTypeExtension.php ./app/Customize/Form/Extension
scp aws:/var/www/eccube/app/Customize/Security/Authenticator/* ./app/Customize/Security/Authenticator/
scp aws:/var/www/eccube/app/Customize/Security/OAuth2/Client/Provider/Line/* ./app/Customize/Security/OAuth2/Client/Provider/Line
scp aws:/var/www/eccube/app/Customize/Security/OAuth2/Client/Provider/Yahoo/* ./app/Customize/Security/OAuth2/Client/Provider/Yahoo

scp aws:/var/www/eccube/app/template/default/Firebase/* ./app/template/default/Firebase/
scp aws:/var/www/eccube/app/template/default/Mypage/login.twig ./app/template/default/Mypage
scp aws:/var/www/eccube/html/user_data/assets/css/customize.css ./html/user_data/assets/css/
scp aws:/var/www/eccube/html/template/default/assets/icon/yahoo.svg ./html/template/default/assets/icon/yahoo.svg
scp aws:/var/www/eccube/html/template/default/assets/icon/line.svg ./html/template/default/assets/icon/line.svg

# @see https://a-zumi.net/eccube4-social-login-no-password/
scp aws:/var/www/eccube/src/Eccube/Resource/template/default/Entry/index.twig  ./src/Eccube/Resource/template/default/Entry/index.twig 
scp aws:/var/www/eccube/src/Eccube/Resource/template/default/Entry/confirm.twig  ./src/Eccube/Resource/template/default/Entry/confirm.twig
scp aws:/var/www/eccube/app/Customize/Form/Extension/EntryTypePasswordExtension.php ./app/Customize/Form/Extension/EntryTypePasswordExtension.php

# 退会処理時 Firebase,Yahoo,Lineのuidを削除
scp aws:/var/www/eccube/src/Eccube/Controller/Admin/Customer/CustomerEditController.php ./src/Eccube/Controller/Admin/Customer/CustomerEditController.php
scp aws:/var/www/eccube/src/Eccube/Controller/Mypage/WithdrawController.php ./src/Eccube/Controller/Mypage/WithdrawController.php

# 会員登録時 session削除
scp aws:/var/www/eccube/app/Customize/EventSubscriber/EntryEventSubscriber.php ./app/Customize/EventSubscriber/EntryEventSubscriber.php

# firebase_uid削除パッチ
# @see https://firebase-php.readthedocs.io/en/latest/user-management.html#list-users
scp aws:/var/www/eccube/app/Customize/Repository/CustomerRepository.php ./app/Customize/Repository/CustomerRepository.php
scp aws:/var/www/eccube/app/template/admin/config.twig ./app/template/admin/config.twig
scp aws:/var/www/eccube/app/Customize/Form/Type/Admin/FirebaseType.php ./app/Customize/Form/Type/Admin/FirebaseType.php
scp aws:/var/www/eccube/app/Customize/Controller/Admin/FirebaseConfigController.php ./app/Customize/Controller/Admin/FirebaseConfigController.php
scp aws:/var/www/eccube/app/Customize/Resource/locale/messages.ja.yaml ./app/Customize/Resource/locale/messages.ja.yaml

# firebase UIの改修
scp aws:/var/www/eccube/app/template/default/Firebase/login_js.twig app/template/default/Firebase/login_js.twig

# Firebase Authenticator の修正(UsernamePasswordTokenの発行)
scp aws:/var/www/eccube/app/Customize/Security/Authenticator/FirebaseJWTAuthenticator.php ./app/Customize/Security/Authenticator/FirebaseJWTAuthenticator.php
```
