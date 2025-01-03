# Hướng dẫn sử dụng API

- T chỉ viết các api mà chưa có ở phần test phía frontend thôi.

## Product

### Tạo sản phẩm mới

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products`.
- Phương thức: POST.

Ví dụ

- Body:

```json
{
    "name": "tên sản phẩm",
    "locationAddress": "địa chỉ/sản phẩm/Việt Nam",
    "description": "mô tả về sản phẩm",
    "price": 3,
    "stock": 4,
    "categoryId": 2,
    "attribute": {
        "màu sắc": ["đen", "đỏ", "vàng", "xanh"],
        "size": ["40", "41", "42", "43"],
        "cỡ dây": ["26 AWG 1 mét", "22 AWG 1 mét", "abc", "xyz"]
    }
}
```

- kết quả trả về:

```json
{
    "id": 14,
    "name": "tên sản phẩm",
    "description": "mô tả về sản phẩm",
    "price": 3,
    "stock": 4,
    "locationAddress": "địa chỉ/sản phẩm/Việt Nam",
    "categoryId": 2,
    "attributes": {
        "màu sắc": [
            "đen",
            "đỏ",
            "vàng",
            "xanh"
        ],
        "size": [
            "40",
            "41",
            "42",
            "43"
        ],
        "cỡ dây": [
            "26 AWG 1 mét",
            "22 AWG 1 mét",
            "abc",
            "xyz"
        ]
    }
}
```

### Xem sản phẩm

#### Xem một sản phẩm dựa vào id

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/{id product}`.
- Phương thức: GET.

Ví dụ

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/10`.

- Kết quả trả về:

```json
{
    "id": 10,
    "name": "tên sản phẩm",
    "description": "mô tả về sản phẩm",
    "prices": null,
    "stock": 0,
    "locationAddress": "địa chỉ/sản phẩm/Việt Nam",
    "categoryId": null,
    "attributes": {
        "màu sắc": [
            "đen",
            "đỏ",
            "vàng",
            "xanh"
        ],
        "size": [
            "40",
            "41",
            "42",
            "43"
        ],
        "cỡ dây": [
            "26 AWG 1 mét",
            "22 AWG 1 mét",
            "abc",
            "xyz"
        ]
    }
}
```

#### Xem tất cả sản phẩm

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products`.

- Kết quả trả về:

```json
[
    {
        "id": 1,
        "name": "xiaomi redme note 4",
        "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
        "prices": null,
        "stock": 0,
        "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 2,
        "name": "xiaomi redme note 4",
        "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
        "prices": null,
        "stock": 0,
        "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 3,
        "name": "xiaomi redme note 4",
        "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
        "prices": 50000,
        "stock": 29628,
        "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43",
                "38",
                "39"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz",
                "nhỏ",
                "vừa",
                "to khủng bố"
            ]
        }
    },
    {
        "id": 4,
        "name": "xiaomi redme note 4",
        "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
        "prices": null,
        "stock": 0,
        "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 5,
        "name": "xiaomi redme note 4",
        "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
        "prices": null,
        "stock": 0,
        "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 6,
        "name": "xiaomi redme note 4",
        "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
        "prices": null,
        "stock": 0,
        "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 7,
        "name": "xiaomi redme note 4",
        "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
        "prices": null,
        "stock": 0,
        "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 8,
        "name": "đổi tên",
        "description": "đổi mô tả, nếu attribute có một khóa thì đổi cả cái mảng đó luôn á",
        "prices": null,
        "stock": 0,
        "locationAddress": "đổi địa chỉ",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đổi",
                "thì",
                "ghi",
                "không",
                "đổi thì thôi"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 9,
        "name": "đổi tên",
        "description": "đổi mô tả, nếu attribute có một khóa thì đổi cả cái mảng đó luôn á",
        "prices": null,
        "stock": 0,
        "locationAddress": "đổi địa chỉ",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đổi",
                "thì",
                "ghi",
                "không",
                "đổi thì thôi"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 10,
        "name": "tên sản phẩm",
        "description": "mô tả về sản phẩm",
        "prices": null,
        "stock": 0,
        "locationAddress": "địa chỉ/sản phẩm/Việt Nam",
        "categoryId": null,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    }
]
```

### Cập nhật sản phẩm

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/{id product}`.
- Phương thức: PUT.

Ví dụ

- Body:

```json
{
    "name": "đổi tên",
    "locationAddress": "đổi địa chỉ",
    "description": "đổi mô tả, nếu attribute có một khóa thì đổi cả cái mảng đó luôn á",
    "attribute": {
        "màu sắc": ["đổi", "thì", "ghi", "không", "đổi thì thôi"]
    }
}
```

- kết quả trả về:

```json
{
    "id": 9,
    "name": "đổi tên",
    "description": "đổi mô tả, nếu attribute có một khóa thì đổi cả cái mảng đó luôn á",
    "prices": null,
    "stock": 0,
    "locationAddress": "đổi địa chỉ",
    "categoryId": null,
    "attributes": {
        "màu sắc": [
            "đổi",
            "thì",
            "ghi",
            "không",
            "đổi thì thôi"
        ],
        "size": [
            "40",
            "41",
            "42",
            "43"
        ],
        "cỡ dây": [
            "26 AWG 1 mét",
            "22 AWG 1 mét",
            "abc",
            "xyz"
        ]
    }
}
```

### Xóa một sản phẩm

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/{id product}`.
- Phương thức: DELETE.

Ví dụ:

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/10`.

```json
{
    "message": "Product deleted"
}
```

### Lấy danh sách sản phẩm từ danh mục sản phẩm

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/by-category/{categoryId}`.
- Phương thức: GET.

Ví dụ:

-Đường dẫn: `https://127.0.0.1:8000/api/products/by-category/2`.

```json
[
    {
        "id": 16,
        "name": "tên sản phẩm",
        "description": "mô tả về sản phẩm",
        "price": 3,
        "stock": 4,
        "locationAddress": "địa chỉ/sản phẩm/Việt Nam",
        "categoryId": 2,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    },
    {
        "id": 17,
        "name": "hello hello",
        "description": "mô tả về sản phẩm",
        "price": 3,
        "stock": 4,
        "locationAddress": "địa chỉ/sản phẩm/Việt Nam",
        "categoryId": 2,
        "attributes": {
            "màu sắc": [
                "đen",
                "đỏ",
                "vàng",
                "xanh"
            ],
            "size": [
                "40",
                "41",
                "42",
                "43"
            ],
            "cỡ dây": [
                "26 AWG 1 mét",
                "22 AWG 1 mét",
                "abc",
                "xyz"
            ]
        }
    }
]
```

### Thêm, cập sửa các thuộc tính và giá trị thuộc tính của sản phẩm

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/{id product}/attribute`.
- Phương thức: POST, PUT.

Ví dụ:

- Đường dẫn `https://127.0.0.1:8000/api/products/3/attribute`.

- Body:

```json
{
    "attribute": ["size", "màu sắc", "cỡ dây"],

    "value": [
        [["38", "xanh", "nhỏ"], [500000, 12345]],
        [["38", "xanh", "vừa"], [600000, 1235]],
        [["39", "đỏ", "to khủng bố"], [50000, 1234]]
        
    ]
}
```

- Kết quả:

```json
{
    "message": "Attributes and options updated successfully"
}
```

### Lấy giá và số lượng hàng của một lựa chọn

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/3/find-option`.
- Phương thức: POST.

Ví dụ:

- Body:

```json
{
    "size": "38",
    "màu sắc": "xanh",
    "cỡ dây": "vừa"

}
```

- Kết quả:

```json
{
    "price": 600000,
    "stock": 1235
}
```

### Lấy giá và số lượng mặc định

- Đường dẫn truy cập `https://127.0.0.1:8000/api/products/{id product}/option-default`.
- Phương thức: GET.

Ví dụ:

- Đường dẫn `https://127.0.0.1:8000/api/products/16/option-default`.

- Kết quả:

```json
{
    "prices": 3,
    "stock": 4
}
```
