# Hướng dẫn sử dụng API

## Product

### Tạo sản phẩm mới

- đường dẫn truy cập `https://127.0.0.1:8000/api/products`.
- Method: POST.
- Body:

```{
    "name": "xiaomi redme note 4",
    "locationAddress": "Hà Đông/Hà Nội/Việt Nam",
    "description": "có thể không gửi trường này và attribute. nếu không gửi description thì nó là null, nếu không gửi attribute thì không có bản ghi được tạo ra",
    "attribute": {
        "màu sắc": ["đen", "đỏ", "vàng", "xanh"],
        "size": ["40", "41", "42", "43"],
        "cỡ dây": ["26 AWG 1 mét", "22 AWG 1 mét", "abc", "xyz"]
    }
}```

### Cập nhật sản phẩm

- đường dẫn truy cập `https://127.0.0.1:8000/api/products/{id}`. 

## 