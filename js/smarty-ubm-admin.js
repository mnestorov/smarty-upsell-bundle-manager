jQuery(document).ready(function($) {
    // Handle tab switching
    $(".smarty-ubm-nav-tab").click(function (e) {
        e.preventDefault();
        $(".smarty-ubm-nav-tab").removeClass("smarty-ubm-nav-tab-active");
        $(this).addClass("smarty-ubm-nav-tab-active");

        $(".smarty-ubm-tab-content").removeClass("active");
        $($(this).attr("href")).addClass("active");
    });

    // Load README.md
    $("#smarty-ubm-load-readme-btn").click(function () {
        const $content = $("#smarty-ubm-readme-content");
        $content.html("<p>Loading...</p>");

        $.ajax({
            url: smartyUpsellBundleManager.ajaxUrl,
            type: "POST",
            data: {
                action: "smarty_ubm_load_readme",
                nonce: smartyUpsellBundleManager.nonce,
            },
            success: function (response) {
                console.log(response);
                if (response.success) {
                    $content.html(response.data);
                } else {
                    $content.html("<p>Error loading README.md</p>");
                }
            },
        });
    });

    // Load CHANGELOG.md
    $("#smarty-ubm-load-changelog-btn").click(function () {
        const $content = $("#smarty-ubm-changelog-content");
        $content.html("<p>Loading...</p>");

        $.ajax({
            url: smartyUpsellBundleManager.ajaxUrl,
            type: "POST",
            data: {
                action: "smarty_ubm_load_changelog",
                nonce: smartyUpsellBundleManager.nonce,
            },
            success: function (response) {
                console.log(response);
                if (response.success) {
                    $content.html(response.data);
                } else {
                    $content.html("<p>Error loading CHANGELOG.md</p>");
                }
            },
        });
    });
});
