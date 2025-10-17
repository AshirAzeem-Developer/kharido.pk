<?php

/**
 * Generates the HTML body for the order confirmation email.
 *
 * @param array $orderData Contains order summary data (number, total, items).
 * @param string $userName The user's first name.
 * @return string The full HTML email body.
 */
function generateOrderConfirmationBody(array $orderData, string $userName): string
{
    $itemsHtml = '';

    // 1. Generate HTML for each item ordered
    foreach ($orderData['items'] as $item) {
        $itemsHtml .= "
        <tr style='background-color: #ffffff; transition: background-color 0.3s ease;'>
            <td style='border: 1px solid #e0e0e0; padding: 14px; color: #333; font-weight: 500;'>{$item['product_name']}</td>
            <td style='border: 1px solid #e0e0e0; padding: 14px; text-align: center; color: #666;'>x{$item['quantity']}</td>
            <td style='border: 1px solid #e0e0e0; padding: 14px; text-align: right; color: #cc9966; font-weight: 700;'>$. " . number_format($item['subtotal'], 2) . "</td>
        </tr>
        ";
    }

    // 2. Return the full HTML template
    return "
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; background: linear-gradient(135deg, #f5f5f5 0%, #efefef 100%); padding: 40px 20px; margin: 0;'>
        <div style='max-width: 650px; margin: auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.1);'>
            
            <!-- Header -->
            <div style='background: linear-gradient(135deg, #cc9966 0%, #b8835c 100%); padding: 35px 30px; text-align: center;'>
                <h1 style='margin: 0; color: white; font-size: 32px; font-weight: 700;'>✓ Order Confirmed!</h1>
                <p style='margin: 10px 0 0 0; color: rgba(255,255,255,0.92); font-size: 15px; font-weight: 500;'>Thank you for your purchase</p>
            </div>

            <!-- Main Content -->
            <div style='padding: 35px 30px;'>
                <p style='color: #333; font-size: 16px; margin: 0 0 8px 0;'>Hi <strong>{$userName}</strong>,</p>
                <p style='color: #666; font-size: 15px; line-height: 1.6; margin: 0 0 28px 0;'>
                    Your order has been successfully received! Here are your complete order details.
                </p>

                <!-- Order Info Table -->
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 30px;'>
                    <tr>
                        <td style='border: 1px solid #e0e0e0; padding: 12px 14px; background-color: #f9f9f9; color: #999; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; width: 50%;'>Order Number</td>
                        <td style='border: 1px solid #e0e0e0; padding: 12px 14px; background-color: #f9f9f9; color: #999; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; width: 50%;'>Order Date</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #e0e0e0; padding: 14px; color: #cc9966; font-size: 18px; font-weight: 700;'>#{$orderData['order_number']}</td>
                        <td style='border: 1px solid #e0e0e0; padding: 14px; color: #333; font-size: 15px; font-weight: 600;'>" . date('M d, Y h:i A') . "</td>
                    </tr>
                </table>

                <!-- Items Table -->
                <p style='color: #999; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px 0; font-weight: 600;'>Order Items</p>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 25px;'>
                    <thead>
                        <tr style='background: linear-gradient(135deg, #f5f5f5 0%, #f0f0f0 100%);'>
                            <th style='border: 1px solid #e0e0e0; padding: 12px 14px; text-align: left; color: #555; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.3px;'>Product</th>
                            <th style='border: 1px solid #e0e0e0; padding: 12px 14px; text-align: center; color: #555; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.3px;'>Qty</th>
                            <th style='border: 1px solid #e0e0e0; padding: 12px 14px; text-align: right; color: #555; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.3px;'>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                </table>

                <!-- Summary Table -->
                <p style='color: #999; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px 0; font-weight: 600;'>Order Summary</p>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 30px;'>
                    <tr style='background-color: #ffffff;'>
                        <td style='border: 1px solid #e0e0e0; padding: 12px 14px; text-align: right; color: #666; font-weight: 600;'>Subtotal:</td>
                        <td style='border: 1px solid #e0e0e0; padding: 12px 14px; text-align: right; color: #333; font-weight: 600;'>$. " . number_format($orderData['final_total'] - $orderData['shipping_cost'], 2) . "</td>
                    </tr>
                    <tr style='background-color: #ffffff;'>
                        <td style='border: 1px solid #e0e0e0; padding: 12px 14px; text-align: right; color: #666; font-weight: 600;'>Shipping Cost:</td>
                        <td style='border: 1px solid #e0e0e0; padding: 12px 14px; text-align: right; color: #333; font-weight: 600;'>$. " . number_format($orderData['shipping_cost'], 2) . "</td>
                    </tr>
                    <tr style='background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);'>
                        <td style='border: 1px solid #e0e0e0; padding: 14px; text-align: right; color: #333; font-weight: 700; font-size: 15px;'>Total Amount:</td>
                        <td style='border: 1px solid #e0e0e0; padding: 14px; text-align: right; color: #cc9966; font-weight: 700; font-size: 16px;'>$. " . number_format($orderData['final_total'], 2) . "</td>
                    </tr>
                </table>

                <!-- Support Message -->
                <div style='background: #f0f8f4; border-left: 4px solid #4caf50; padding: 15px; border-radius: 4px; margin-bottom: 25px;'>
                    <p style='margin: 0; color: #2e7d32; font-size: 14px; line-height: 1.6;'><strong>Next Steps:</strong> We're preparing your items for shipment. You'll receive a tracking number via email once your order ships.</p>
                </div>

                <p style='color: #666; font-size: 14px; line-height: 1.6; margin: 0;'>
                    If you have any questions about your order, please don't hesitate to contact our support team.
                </p>
            </div>

            <!-- Footer -->
            <div style='background: linear-gradient(135deg, #f9f9f9 0%, #f5f5f5 100%); padding: 25px 30px; text-align: center; border-top: 1px solid #e0e0e0;'>
                <p style='margin: 0 0 8px 0; color: #333; font-weight: 700; font-size: 15px;'>Kharido.pk</p>
                <p style='margin: 0; color: #999; font-size: 13px;'>Your trusted online shopping partner</p>
                <p style='margin: 12px 0 0 0; color: #bbb; font-size: 11px;'>&copy; " . date('Y') . " Kharido.pk. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";
}
