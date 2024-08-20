# Yii Auth JWT Change Log

## 2.0.2 August 20, 2024

- Chg #82: Replace `web-token/*` with `web-token/jwt-library` and update min version of PHP to 8.1 (@rustamwin)
- Enh #75: Add support for `psr/http-message` version `^2.0` (@vjik)

## 2.0.1 September 19, 2023

- Bug #53: Add missed dependencies `psr/http-message`, `yiisoft/http` and `web-token/jwt-core` (@vjik)

## 2.0.0 February 13, 2023

- Chg #59: Adapt configuration group names to Yii conventions (@vjik)

## 1.0.3 July 27, 2021

- Fix #43: Added exception handling in `TokenRepository::getClaims()` with invalid token string (@strorch)

## 1.0.2 April 13, 2021

- Chg: Adjust config for `yiisoft/factory` changes (@samdark)

## 1.0.1 March 23, 2021

- Chg: Adjust config for new config plugin (@samdark)

## 1.0.0 February 11, 2021

- Initial release.
