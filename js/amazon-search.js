function eggnstone_widgets_fill_amazon_search_box_for_search_forms(index, url, keywords, footerMessage)
{
    //console.log("eggnstone_widgets_fill_amazon_search_box_for_search_forms");

    const elementName = "amazon-search-" + index;
    const element = document.getElementById(elementName);
    if (!element)
    {
        console.error("Widgets for Amazon: Could not find element '" + elementName + "'. (3)");
        return;
    }

    if (eggnstone_widgets_has_some_parent_the_class(element, "header"))
    {
        //console.log("Ignoring search form in the header.");
        return;
    }

    if (eggnstone_widgets_has_some_parent_the_class(element, "footer"))
    {
        //console.log("Ignoring search form in the footer.");
        return;
    }

    const isNoResultsPage = eggnstone_widgets_has_some_parent_the_class(element, "no-results");
    const is404Page = eggnstone_widgets_has_some_parent_the_class(element, "error404");
    if (!isNoResultsPage && !is404Page)
        return;

    eggnstone_widgets_fill_amazon_search_box(index, url, keywords, footerMessage);
}

function eggnstone_widgets_fill_amazon_search_box(index, url, keywords, footerMessage)
{
    const elementName = "amazon-search-" + index;

    const req = new XMLHttpRequest();
    const full_url = url + "&Keywords=" + keywords;

    req.open("GET", full_url);
    req.addEventListener("load", function ()
    {
        const element = document.getElementById(elementName);
        if (!element)
        {
            console.error("Widgets for Amazon: Could not find element '" + elementName + "'. (2)");
            return;
        }

        if (req.status === 200)
        {
            const maxTitleWidth = element.clientWidth - (100 + 4 * 8 + 2); // 100px image column, 4 * 8px padding, 2 * 1px border
            const parsedJson = JSON.parse(req.responseText);
            const items = parsedJson["Items"];
            const messages = parsedJson["Messages"];

            if (!items || items.length === 0)
                element.innerHTML = eggnstone_widgets_create_search_box_table_with_message(index, url, keywords, "Nothing found.", footerMessage);
            else
                element.innerHTML = eggnstone_widgets_create_search_box_table_with_result(index, url, keywords, items, maxTitleWidth, footerMessage);

            if (messages)
            {
                for (const message of messages)
                {
                    if (message.type === "Error")
                        console.error("Widgets for Amazon: " + message.text);
                    if (message.type === "Warning")
                        console.error("Widgets for Amazon: Warning: " + message.text);
                    else
                        console.log("Widgets for Amazon: " + message.text);
                }
            }
        }
        else
            element.innerHTML = eggnstone_widgets_create_search_box_table_with_message(index, url, keywords, "Nothing found.", footerMessage);
    });

    const element = document.getElementById(elementName);
    if (element)
        element.innerHTML = eggnstone_widgets_create_search_box_table_with_message(index, url, keywords, "Loading ...", footerMessage);
    else
        console.log("Widgets for Amazon: Could not find element '" + elementName + "'. (1)");

    req.send(null);
}

function eggnstone_widgets_refill_amazon_search_box(index, url, footerMessage)
{
    const keywords = document.getElementById("amazon-search-input-" + index).value;
    eggnstone_widgets_fill_amazon_search_box(index, url, keywords, footerMessage);
}

function eggnstone_widgets_on_search_button_click(index, url, footerMessage)
{
    eggnstone_widgets_refill_amazon_search_box(index, url, footerMessage);
}

function eggnstone_widgets_on_search_text_key_up(key, index, url, footerMessage)
{
    if (key === "Enter")
        eggnstone_widgets_refill_amazon_search_box(index, url, footerMessage);
}

function eggnstone_widgets_create_search_box_table_with_message(index, url, keywords, message, footerMessage)
{
    let html = eggnstone_widgets_create_search_box_table_start(index, url, keywords, footerMessage);

    html += "" +
        "<tr>" +
        "  <td class='left-cell' style='width: 0;'>" +
        "  </td>" +
        "  <td class='right-cell' style='text-align: center; padding: 16px;'>" +
        "    <span class='product-title'>" + message + "</span>" +
        "  </td>" +
        "</tr>";

    html += eggnstone_widgets_create_search_box_table_end(footerMessage);

    return html;
}

function eggnstone_widgets_create_search_box_table_with_result(index, url, keywords, items, maxTitleWidth, footerMessage)
{
    let html = eggnstone_widgets_create_search_box_table_start(index, url, keywords, footerMessage);

    for (const item of items)
    {
        const title = item["title"];
        const price = item["price"];
        const productUrl = item["productUrl"];
        const imageUrl = item["imageUrl"];
        const rating = item["rating"];
        const ratingText = item["ratingText"];
        const reviewCount = item["reviewCount"];

        let starsDiv = "<div role='img' title='" + ratingText + "' class='stars'>";

        for (let i = 1; i <= Math.floor(rating + 0.2); i++)
            starsDiv += "<span class='star on'></span>";

        if (rating % 1 >= 0.29 && rating % 1 <= 0.71)
            starsDiv += "<span class='star half'></span>";

        for (let i = Math.ceil(rating + 0.8); i <= 5; i++)
            starsDiv += "<span class='star'></span>";

        starsDiv += "</div>";

        const titleSpan = "<span class='product-title' style='width: " + maxTitleWidth + "px;'>" + title + "</span>";
        const priceSpan = "<span class='product-price'>" + price + "</span>";
        const reviewCountSpan = "<span class='product-review-count'>(" + reviewCount + ")</span>";

        html += "" +
            "<tr>" +
            "  <td class='left-cell'>" +
            "    <a class='product-link' target='_blank' href='" + productUrl + "'>" +
            "      <img alt='' style='max-height: 50px; max-width:100px;' src='" + imageUrl + "' />" +
            "    </a>" +
            "  </td>" +
            "  <td class='right-cell'>" +
            "    <a class='product-link' target='_blank' href='" + productUrl + "'>" +
            "      " + titleSpan +
            "      " + priceSpan + starsDiv + reviewCountSpan +
            "    </a>" +
            "  </td>" +
            "</tr>";
    }

    html += eggnstone_widgets_create_search_box_table_end(footerMessage);

    return html;
}

function eggnstone_widgets_create_search_box_table_start(index, url, keywords, footerMessage)
{
    return "<br/>" +
        "<table class='outer-table'>" +
        "  <tr>" +
        "    <td colspan='2' class='top-row'>" +
        "      <table class='inner-table'>" +
        "        <tr>" +
        "          <td class='left-cell'>" +
        "            <div class='input-border'>" +
        "              <label>" +
        "                <input id='amazon-search-input-" + index + "' value='" + keywords + "'" +
        "                  onkeyup='eggnstone_widgets_on_search_text_key_up(event.key, \"" + index + "\", \"" + url + "\", \"" + footerMessage + "\");'/>" +
        "              </label>" +
        "            </div>" +
        "          </td>" +
        "          <td class='right-cell'>" +
        "            <button " +
        "              onclick='eggnstone_widgets_on_search_button_click(\"" + index + "\", \"" + url + "\", \"" + footerMessage + "\");'/>" +
        "              <svg width='12' height='12' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M500.3 443.7l-119.7-119.7c27.22-40.41 40.65-90.9 33.46-144.7C401.8 87.79 326.8 13.32 235.2 1.723C99.01-15.51-15.51 99.01 1.724 235.2c11.6 91.64 86.08 166.7 177.6 178.9c53.8 7.189 104.3-6.236 144.7-33.46l119.7 119.7c15.62 15.62 40.95 15.62 56.57 0C515.9 484.7 515.9 459.3 500.3 443.7zM79.1 208c0-70.58 57.42-128 128-128s128 57.42 128 128c0 70.58-57.42 128-128 128S79.1 278.6 79.1 208z' style='fill:#ffffff'/></svg>" +
        "            </button>" +
        "          </td>" +
        "        </tr>" +
        "      </table>" +
        "    </td>" +
        "  </tr>";
}

function eggnstone_widgets_create_search_box_table_end(footerMessage)
{
    return "</table>" + '<div class="footer">' + footerMessage + "</div>";
}

function eggnstone_widgets_has_some_parent_the_class(element, classname)
{
    if (element.className && element.className.split(" ").indexOf(classname) >= 0)
        return true;

    return element.parentNode && eggnstone_widgets_has_some_parent_the_class(element.parentNode, classname);
}
