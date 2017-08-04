<?php
namespace  vendor\alipay;
class AlipayConfig{
    public  function  alipayconfig(){
        $config = array (
            //应用ID,您的APPID。
            'app_id' => "2017080208002347",
            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => "MIIEogIBAAKCAQEA1fLGsEeAew9wREY1BO225GAgBwIiPoLX/kZ8BxYMXinICAVk/N0Px8T25StcSmgG5fGUB8j0Cg8R0IHYHi2USV/ecbvPgUYOXD0O8Lsz4FSvpZ8OiULIWbhOO6/WJNTZ/IadSC04KGKyDXc2JMRUFFyu5sQPUBzCeioYzKdwAH2qBh3tno2N9WSZ6WZA9IJn9Fex1IMa1pwc8V2X1KEv2rNKkA+pQ0tIWkaR1DehjdMHi6vSLdvqgLSaJ0CNdrttQfZtDqUpczBOvs5WoGTnmZvR2GRFyHMREJ/ewc2C5niJh8DXApmVKe5SfoWi00yyugub5CP63D+RYcP2n2+y2QIDAQABAoIBACTIFzaJJGKDtfZW55iQUagTio4J5N9AWWfisEH7nWa24ifW3ZTHYIYkq5mcicJU/hKcJKnt916fVp21JPWTOnIe5DxDYJpF9AQRjzb9yB5iEMx1eudILu+VywVh5nvwiAVtEy8sbwIqbZf5DbrmcdoKURtlS4inWRvtjtPoWU4L/CKCOyOVXeDrsDYLEHK5JgQOuq2v5y+9fz6VE1APx/hFUBwznFroT1qzqcgeaoGxIT2CBPAr9NVcjE2UEJfZV7tekegCCECTBAbqDRIyihA2Z4/uW9TgSMndQys2uErjmDpPz3910B3dbKAbIewCxMTNBpHyi4jgs4Mj9ciCejECgYEA8ehwNioWetPtFXZFxYPmEcUCTGpi7ipwe0oG3GxNQ9JYGAvNAotmujbFO7Y9asyVWQ9oveXHpHpHOh62jbL+7raNmIzDlI/uSWmgRxkV/NKeIaF9TlfDv+lUJ0Q+l/jec3NC6TU6vhqFk1xQDgjqxBXQVZEMa6LOp6fGrriyDRUCgYEA4mlgm9pV6fnfpU+KCKg3+6Lao68YFq++rv09jzBdAoC0QYA+uwTytGDUU3azA5tfeDZvDrATnVAUrdnFOrpmOzpmx2gjscZ8mu5L5No6E2iS27fNLvFaO9Cc95OCbqblYbibPl2TMwsC/WaDyXpR6zKbfZsdnXYCrvkZI4llZ7UCgYB6pXk2zPHmxkm+ht9q+6uKuNKogYu06olqyKZf3PzzdNK7JkiDkL+9i4VF9+h9nEngBIL7PGOSzXgNTIMLpoO/7YgunreOO8b5K3dadKqEKxiHPoP0U2ToiNjkq6H8lO3bpMV4zUcCGRo+EyB3bSfx0Il2yHp+m+WgdNyzuupy3QKBgGqT9DgpRPwI9bAZiv6cQQ4hEQ6wI4S6YeZ5qvq2Z3IAJ9oVhnH+2Ej0s74+R/JQt1YdOZYOaI1K3xEfS+pjSDBlQ3BmozbSGmHL/snRuDjepxLqqJmKX1F82Abq1yFDSwL/JYTsA5ipfFTQZwT2oY03m147IGu423aGJ8FEsglpAoGAMKBb1TAdozRNLv1BqhSBiC9Bnr0VTWlZXIdg3Quj0wS09IdiGXmoGcJ565RyUYLoxxfQ8QvNAF89T1gAwz8/qy3JXvos0NFyMewOSzoNqEoAc9w2GqpWAqnbrEmtszrMB35VX0KrZgNTIp5TAHVg6h44AdkaIsg8agoEm6YADlA=",

            //异步通知地址
            'notify_url' => "http://test.cdlhzz.cn",

            //同步跳转
            'return_url' => "http://www.baidu.com",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAitSci0DShwM4TpRrl8wRNw+SL26aoy3i/TU/6XuiKUXVX1NwVCxnmI8zriv4ZQEZamVy8K4rrI38slqAnKRNbDLfMJ1DnbKZR4z5LTrV4vn1nfHcUEfu8mRP30zN6nbH/8PbsU1+6uaU0l5hyemstC7oP0zv2Vo1ZLkMHDNipVnxT8nlCRUJGAQJDZF+5KD6VBhrrdf/+IfXgWBl0JTZG0r2BW1SPNmGstol3dY3yGjyVZXlHZBV0w27A36jF30Eg4Nz6FKl2DV9XrUMbjGJmvVJ8Ej+Tu6uyRvO7DRbrDE+huSYRbxAc3c18E6J9aWDtMSBeaX5MH0Ih7ioMtnN5wIDAQAB",

        );
        return $config;
    }
}