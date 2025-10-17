<?php

/**
 * Generates the HTML body for the order confirmation email.
 *
 * @param array $orderData Contains order summary data (number, total, items).
 * @param string $userName The user's fi$t name.
 * @return string The full HTML email body.
 */
function generateOrderConfirmationBody(array $orderData, string $userName): string
{
    $itemsHtml = '';

    // 1. Generate HTML for each item ordered
    foreach ($orderData['items'] as $item) {
        $itemsHtml .= "
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>{$item['product_name']} (x{$item['quantity']})</td>
            <td style='border: 1px solid #ddd; padding: 8px; text-align: right;'>$. " . number_format($item['subtotal'], 2) . "</td>
        </tr>
        ";
    }

    // 2. Return the full HTML template
    return "
    <html>
    <body style='font-family: sans-serif; background-color: #f4f4f4; padding: 20px;'>
        <div style='max-width: 600px; margin: auto; background: white; padding: 20px; border: 1px solid #ccc;'>
            <h2 style='text-align: center; color: #cc9966;'>Order Confirmation from Kharido.pk</h2>
            
            <p>Dear {$userName},</p>
            
            <p>Thank you for your order! Your order details are below:</p>
            
            <p style='font-size: 1.1em; font-weight: bold;'>Order #{$orderData['order_number']}</p>
            <p>Order Date: " . date('M d, Y h:i A') . "</p>

            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <thead>
                    <tr style='background-color: #f9f9f9;'>
                        <th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Product</th>
                        <th style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;'>Shipping:</td>
                        <td style='border: 1px solid #ddd; padding: 8px; text-align: right;'>$. " . number_format($orderData['shipping_cost'], 2) . "</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; background-color: #eee;'>Total Amount:</td>
                        <td style='border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; background-color: #eee;'>$. " . number_format($orderData['final_total'], 2) . "</td>
                    </tr>
                </tbody>
            </table>

            <p style='margin-top: 20px;'>
                If you have any questions, please contact us.
            </p>
            <p style='text-align: center; font-size: 0.8em; color: #888;'>&copy; " . date('Y') . " Kharido.pk</p>
        </div>
    </body>
    </html>";
}
