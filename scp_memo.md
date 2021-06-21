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
# scp aws:/var/www/eccube/app/Customize/Security/OAuth2/Client/Provider/Line/* ./app/Customize/Security/OAuth2/Client/Provider/Line
# scp aws:/var/www/eccube/app/Customize/Security/OAuth2/Client/Provider/Yahoo/* ./app/Customize/Security/OAuth2/Client/Provider/Yahoo

scp aws:/var/www/eccube/app/template/default/Firebase/* ./app/template/default/Firebase/
scp aws:/var/www/eccube/app/template/default/Mypage/login.twig ./app/template/default/Mypage
# scp aws:/var/www/eccube/html/user_data/assets/css/customize.css ./html/user_data/assets/css/
# scp aws:/var/www/eccube/html/template/default/assets/icon/yahoo.svg ./html/template/default/assets/icon/yahoo.svg
# scp aws:/var/www/eccube/html/template/default/assets/icon/line.svg ./html/template/default/assets/icon/line.svg
```
