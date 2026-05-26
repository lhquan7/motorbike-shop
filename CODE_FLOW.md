# Luồng code từng chức năng của MotoShop

## 1. Tổng quan
Tài liệu này giải thích luồng hoạt động chính của hệ thống MotoShop, mô tả các chức năng chính, các route, controller, service và mô hình dữ liệu liên quan.

---

## 2. Luồng route và điều phối
Các route chính nằm trong `routes/web.php`.

- `GET /` → `HomeController@index`: trang chủ.
- `GET /shop` → `ShopController@index`: danh sách sản phẩm.
- `GET /shop/{product:slug}` → `ShopController@show`: chi tiết sản phẩm.
- `GET /search/ajax` → `SearchController@ajax`: tìm kiếm sản phẩm AJAX.

Cart:
- `GET /cart` → `CartController@index`
- `POST /cart/{product}` → `CartController@add`
- `DELETE /cart/{id}` → `CartController@remove`
- `POST /cart/clear` → `CartController@clear`

Checkout:
- `GET /checkout` → `CheckoutController@index`
- `POST /checkout` → `CheckoutController@store`
- `GET /checkout/success/{orderCode}` → `CheckoutController@success`

Payment online:
- `GET /payment/vnpay/redirect` → `PaymentController@redirectVNPay`
- `GET /payment/momo/redirect` → `PaymentController@redirectMoMo`
- `GET /payment/vnpay/return` → `PaymentController@vnpayReturn`
- `GET /payment/momo/return` → `PaymentController@momoReturn`
- `POST /payment/momo/notify` → `PaymentController@momoNotify`

Profile:
- `GET /profile` → `ProfileController@index`
- `PUT /profile/update` → `ProfileController@update`
- `PUT /profile/change-password` → `ProfileController@changePassword`
- `GET /profile/orders/{orderCode}` → `ProfileController@orderDetail`

Admin:
- prefix `admin/`, middleware `auth` và `admin`
- `admin/dashboard`, resource management: `products`, `categories`, `brands`, `orders`, `posts`
- report export: `reports`, `reports/export/orders`, `reports/export/revenue`, `reports/export/pdf`

---

## 3. Giỏ hàng (Cart)
Chức năng giỏ hàng được lưu trong session.

- `CartController@index`
  - Hiển thị session `cart` hiện tại.
  - Nếu giỏ trống, redirect về trang cart với thông báo lỗi.

- `CartController@add`
  - Thêm sản phẩm vào session `cart`.
  - Cập nhật số lượng nếu sản phẩm đã tồn tại.

- `CartController@remove`
  - Xóa sản phẩm cụ thể khỏi giỏ.

- `CartController@clear`
  - Xóa toàn bộ giỏ hàng trong session.

---

## 4. Checkout và tạo đơn hàng
Luồng chính nằm trong `app/Http/Controllers/CheckoutController.php`.

### `CheckoutController@index`
- Hiển thị form checkout.
- Lấy giỏ hàng từ `session('cart', [])`.
- Nếu giỏ hàng trống, redirect về cart.

### `CheckoutController@store`
Các bước chính:
1. Validate dữ liệu khách hàng: tên, số điện thoại, địa chỉ, email, phương thức thanh toán, ghi chú.
2. Kiểm tra giỏ hàng còn tồn tại.
3. Tính tổng tiền từ `price * quantity` từng item.
4. Tạo `Order` với trạng thái `pending` và `payment_status = unpaid`.
5. Tạo `OrderItem` cho từng sản phẩm và trừ tồn kho `stock`.
6. Xóa session giỏ hàng.
7. Redirect theo phương thức thanh toán:
   - `vnpay` → `payment.vnpay.redirect`
   - `momo` → `payment.momo.redirect`
   - `cod`, `bank_transfer` → gửi email xác nhận và chuyển hướng `checkout.success`

### `CheckoutController@success`
- Lấy order theo `order_code`.
- Kiểm tra nếu người dùng đã đăng nhập thì chỉ cho xem đơn của chính họ.
- Hiển thị view `checkout.success`.

---

## 5. Thanh toán online
Thanh toán online được xử lý trong `app/Http/Controllers/PaymentController.php` và các service `app/Services/VNPayService.php`, `app/Services/MoMoService.php`.

### PaymentController
- `redirectVNPay`
  - Lấy order theo `order_code`.
  - Gọi `VNPayService::createPaymentUrl()` để tạo URL thanh toán.
  - Redirect sang cổng VNPay.

- `vnpayReturn`
  - Nhận callback từ VNPay.
  - Xác thực chữ ký với `VNPayService::verifyReturn()`.
  - Lấy `order_code` từ `vnp_TxnRef`.
  - Nếu thành công, cập nhật order `payment_status = paid`, `status = confirmed`.
  - Nếu có email khách, gửi `OrderConfirmationMail`.
  - Nếu thất bại, cập nhật `status = cancelled` và báo lỗi.

- `redirectMoMo`
  - Lấy order và gọi `MoMoService::createPayment()`.
  - Nếu có `payUrl`, redirect sang MoMo.
  - Nếu lỗi, trả về với thông báo.

- `momoReturn`
  - Callback redirect từ MoMo.
  - Xác thực chữ ký bằng `MoMoService::verifyReturn()`.
  - Lấy order từ `orderId` và cập nhật trạng thái tương tự.

- `momoNotify`
  - Callback server-to-server (IPN) từ MoMo.
  - Xác thực chữ ký và cập nhật order khi thanh toán thành công.

### VNPayService
- `createPaymentUrl()`
  - Tạo tham số request VNPay: version, command, tmnCode, amount, locale, return URL, IP, create/expire date.
  - Sắp xếp tham số, tạo HMAC SHA512 và trả về URL đầy đủ.

- `verifyReturn()`
  - Loại bỏ `vnp_SecureHash`, sắp xếp tham số, tạo HMAC và so sánh với chữ ký trả về.

- `isSuccess()`
  - Kiểm tra `vnp_ResponseCode === '00'`.

### MoMoService
- `createPayment()`
  - Tạo `requestId`, `extraData`, `redirectUrl`, `ipnUrl`.
  - Tạo chuỗi `rawHash` cho MoMo và chữ ký HMAC SHA256.
  - Gửi POST tới endpoint MoMo và trả về JSON response.

- `verifyReturn()`
  - Tạo `rawHash` từ dữ liệu trả về MoMo, so sánh chữ ký.

- `isSuccess()`
  - Kiểm tra `resultCode === 0`.

---

## 6. Gửi email xác nhận đơn hàng
Các email chính nằm trong `app/Mail/OrderConfirmationMail.php` và `app/Mail/OrderStatusUpdatedMail.php`.

- `OrderConfirmationMail`
  - Gửi mail khi đơn hàng được tạo hoặc thanh toán thành công.
  - Sử dụng dữ liệu `Order` và `OrderItem`.

- `OrderStatusUpdatedMail`
  - Gửi mail khi trạng thái đơn hàng thay đổi.

---

## 7. Cấu trúc models quan trọng
### `Order`
- `fillable`: `order_code`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `customer_address`, `total_amount`, `status`, `payment_method`, `payment_status`, `note`.
- Quan hệ:
  - `items()` → `hasMany(OrderItem::class)`
  - `user()` → `belongsTo(User::class)`

### `OrderItem`
- Lưu chi tiết từng sản phẩm trong đơn.
- `order_id` liên kết đến `orders`, `product_id` liên kết đến `products`.

### `Product`
- Lưu thông tin xe: `category_id`, `brand_id`, `price`, `sale_price`, `stock`, `engine`, `color`, `year`, `description`, `image`, `images`, `is_featured`, `is_active`.

### `Category`, `Brand`, `Post`, `User`
- Các model chuẩn dùng để phân loại, quản lý bài viết, người dùng.

---

## 8. Migrations chính
Các bảng quan trọng:
- `users`: thông tin người dùng.
- `products`: thông tin sản phẩm.
- `categories`, `brands`: phân loại sản phẩm.
- `orders`: đơn hàng.
- `order_items`: chi tiết sản phẩm trong đơn.
- `posts`: bài viết.
- `cache`, `jobs`, `sessions`: hỗ trợ hệ thống Laravel.

Bao gồm các migration bổ sung:
- `add_role_to_users_table.php`: thêm role, phone, address cho users.
- `add_product_name_to_order_items_table.php`: thêm chi tiết sản phẩm cho order_items.
- `add_columns_to_orders_table.php`: thêm trường mã đơn, thông tin khách hàng, trạng thái và phương thức thanh toán.

---

## 9. Luồng xử lý chính khi khách hàng mua hàng
1. Khách xem sản phẩm ở `/shop` hoặc trang chi tiết.
2. Thêm sản phẩm vào giỏ (`CartController@add`).
3. Đi tới checkout (`CheckoutController@index`).
4. Gửi form checkout (`CheckoutController@store`).
5. Tạo `Order` và `OrderItem`, cập nhật tồn kho.
6. Nếu thanh toán online:
   - Redirect sang VNPay hoặc MoMo.
   - Nhận callback, xác thực chữ ký, cập nhật trạng thái.
7. Nếu thanh toán offline: gửi mail xác nhận và hiển thị trang success.

---

## 10. Lưu ý quan trọng
- Cơ chế thanh toán online cần cấu hình chính xác trong `config/services.php`.
- Email xác nhận được gửi theo `customer_email` nếu có.
- `auth` và `admin` middleware bảo vệ route profile và admin.
- Migrations bổ sung thay đổi schema theo yêu cầu hệ thống.

---

## 11. Những file chính để tham khảo nhanh
- `routes/web.php`
- `app/Http/Controllers/CheckoutController.php`
- `app/Http/Controllers/PaymentController.php`
- `app/Services/VNPayService.php`
- `app/Services/MoMoService.php`
- `app/Models/Order.php`
- `app/Mail/OrderConfirmationMail.php`
- `database/migrations/*`