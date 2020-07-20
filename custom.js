var ajax_url = js_data.ajaxurl;
var currency_symbol = js_data.currency_symbol;

// Managing Cart Totals in the beginning 
var iE = jQuery("ul.cart-item li").each(function () {
    var iPid = jQuery(this).find(".food-item").data("product-id");
    var iP = jQuery(this).find(".price").data("product-price");
    var iQ = jQuery(this).find(".quantity").text().match(/\d+/)[0];
    var fiQ = parseInt(iQ);
    var fiP = parseInt(iP);
    var newPrice = fiQ *= fiP;
    jQuery("<span>£</span>").insertBefore(jQuery(this).find(".price"));
    jQuery(this).find(".price").text(newPrice);
});

// 
jQuery(".woofood-accordion .add_to_cart_button").text("+");
jQuery(".panel-collapse").addClass("show");

// Cart Properties
var itemsId = [];
jQuery(".cart-item li").each(function () {
    var item = jQuery(this).find(".food-item");
    var id = item.data("product-id");
    itemsId.push(id);
});
console.log(itemsId);


// Adding to Cart from Menu 
jQuery("body").on("click", ".add_to_cart_button", function (e) {
    e.preventDefault();
    var target = jQuery(".cart-item");
    var parent = e.target.parentNode.parentNode.parentNode;
    var productId = jQuery(e.target).data('product_id');
    var productTitle = jQuery(parent).children(".product-title").text();
    var productPrice = jQuery(parent).children(".product-price").text().match(/\d+/)[0];
    console.log(productPrice);
    var quantity = 1;
    // Adding to cart
    // If product already exists then update quantity 
    if (!itemsId.includes(productId)) {
        itemsId.push(productId);
        var productItem = `<li><div class="buttons"><button class="decrease">-</button><button class="increase">+</button></div><div class="item-details"><div class="quantity"> ${quantity}x</div><div class="food-item" data-product-id="${productId}"> ${productTitle} </div><span>£</span><div class="price" data-product-price="${productPrice}">${productPrice}</div></div></li>`;
        target.append(productItem);
        setTimeout(function () {
            jQuery("#ref-calc").load(window.location.href + " #ref-calc");
        }, 200);
    } else {
        var productId = productId;
        var element = jQuery("ul.cart-item").find(`.food-item[data-product-id="${productId}"]`).parent().parent();
        var quantity = jQuery(element).find(".quantity").text().match(/\d+/)[0];
        var fQuantity = parseInt(quantity);
        jQuery(element).find(".quantity").text(fQuantity + 1 + "x");
        // Update the Price aswell
        var oldPrice = parseInt(jQuery(element).find(".price").text());
        var fNewPrice = parseInt(productPrice);
        var newPrice = fNewPrice += oldPrice;
        jQuery(element).find(".price").text(newPrice);
        jQuery.ajax({
            url: ajax_url,
            type: 'post',
            data: {
                action: 'increase_quantity',
                productId: productId,
            },
            success: function (response) {
                setTimeout(function () {
                    jQuery("#ref-calc").load(window.location.href + " #ref-calc");
                }, 50);
            }
        })
    }
});

// Update Quantity and Price on Increase
jQuery("body").on("click", ".increase", function (e) {
    e.preventDefault();
    var buttonParent = e.target.parentNode.parentNode;
    var productId = jQuery(buttonParent).find(".food-item").data("product-id");
    var sQ = jQuery(buttonParent).find(".quantity");
    var eQ = sQ.text().match(/\d+/)[0];
    var nQ = parseInt(eQ);
    var uQ = nQ + 1;
    sQ.text(uQ + "x");

    // Now Price
    var actualPrice = jQuery(buttonParent).find(".price").data("product-price"),
        currentPrice = jQuery(buttonParent).find(".price").text(),
        formattedPrice = parseInt(currentPrice);
    var changePrice = jQuery(buttonParent).find(".price");
    changePrice.text(actualPrice += formattedPrice);
    
    // Now Subtotal and Total

    // Realtime Change 
    var totalPrice = actualPrice += formattedPrice;
    jQuery("#subtotal .amount").text("£" + totalPrice);
    jQuery("#total .amount").text("£" + totalPrice);
    // Update through Ajax
    setTimeout(function () {
        jQuery("#ref-calc").load(window.location.href + " #ref-calc");
    }, 200);
    // Add to cart via ajax 
    jQuery.ajax({
        url: ajax_url,
        type: 'post',
        data: {
            productId: productId,
            quantity: uQ,
            action: "add_to_cart_increase_button",
        },
        success: function (response) {
            console.log(response);
        }
    });

});


// Decrease Quantity
jQuery("body").on("click", ".decrease", function (e) {
    e.preventDefault();
    var buttonParent = e.target.parentNode.parentNode;
    var productId = jQuery(buttonParent).find(".food-item").data("product-id");
    var sQ = jQuery(buttonParent).find(".quantity");
    var cartHash = jQuery(buttonParent).find(".quantity").data("product-cart-hash");
    var eQ = sQ.text().match(/\d+/)[0];
    var nQ = parseInt(eQ);

    if (nQ <= 1 && !cartHash) {
        buttonParent.remove();
        const index = itemsId.indexOf(productId);
        itemsId.splice(index, 1);
        jQuery.ajax({
            url: ajax_url,
            type: "post",
            data: {
                productId: productId,
                action: 'remove_product_generate_key',

            },
            success: function (response) {
                jQuery("#ref-calc").load(window.location.href + " #ref-calc");
            }
        });
    } else if (nQ <= 1 && cartHash) {
        buttonParent.remove();
        const index = itemsId.indexOf(productId);
        itemsId.splice(index, 1);
        var uQ = nQ - 1;
        sQ.text(uQ + "x");
        jQuery("#subtotal .amount").text("0");
        jQuery("#total .amount").text("0");
        // Add to cart via ajax 
        jQuery.ajax({
            url: ajax_url,
            type: 'post',
            data: {
                productId: productId,
                cartHash: cartHash,
                quantity: uQ,
                action: "add_to_cart_decrease_button",
            },
            success: function (response) {
                console.log(response);
            }
        });
    } else {
        var uQ = nQ - 1;
        sQ.text(uQ + "x");
        // Now Price
        var actualPrice = jQuery(buttonParent).find(".price").data("product-price"),
            currentPrice = jQuery(buttonParent).find(".price").text(),
            formattedPrice = parseInt(currentPrice);
        var changePrice = jQuery(buttonParent).find(".price");
        changePrice.text(formattedPrice -= actualPrice);


        // Now Subtotal and Total
        setTimeout(function () {
            jQuery("#ref-calc").load(window.location.href + " #ref-calc");
        }, 500);
        // Add to cart via ajax 
        jQuery.ajax({
            url: ajax_url,
            type: 'post',
            data: {
                productId: productId,
                cartHash: cartHash,
                quantity: uQ,
                action: "add_to_cart_decrease_button",
            },
            success: function (response) {
                console.log(response);
            }
        });

    }
});



// Sticky Properties +
jQuery(window).scroll(function () {
    var height = jQuery(window).scrollTop();
    var o = jQuery(".cart-widget");
    o2 = jQuery("ul.product-cats");
    if (height > 430) {
        o.addClass("sticky");
        o2.addClass("sticky");
    } else if (height < 430) {
        o.removeClass("sticky");
        o2.removeClass("sticky");
    }
});