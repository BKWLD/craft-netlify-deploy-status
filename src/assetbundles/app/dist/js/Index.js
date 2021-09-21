/**
 * Craft Netlify Deploy Status plugin for Craft CMS
 *
 *
 * @author    Bukwild
 * @copyright Copyright (c) 2021 Bukwild
 * @link      https://bukwild.com
 * @package   CraftNetlifyDeployStatus
 * @since     1.0.0
 */

(function ($) {
    /** global: Craft */
    /** global: Garnish */
    var Manager = Garnish.Base.extend(
        {
            timeout: null,

            init: function () {
                this.checkIsDeploying()
            },

            checkIsDeploying: function () {
                Craft.postActionRequest('craft-netlify-deploy-status/status/is-deploying', {}, $.proxy(function (response, textStatus) {
                    if (textStatus === 'success') {
                        if (response.isDeploying){
                            this.changeNavIcon(true)
                        }else{
                            this.changeNavIcon(false)
                            if (this.timeout) clearTimeout(this.timeout)
                        }
                    }
                }, this));
            },

            changeNavIcon: function (loading = true) {
                let iconContainer = document.querySelector('#nav-craft-netlify-deploy-status .icon')
                if (!iconContainer) return

                if (loading) {
                    let icon = iconContainer.querySelector('svg')
                    icon.style.display = 'none'

                    let loadingContainer = iconContainer.querySelector('div');
                    if (!loadingContainer) {
                        loadingContainer = document.createElement("div")
                        loadingContainer.classList.add('nav-loader')
                        iconContainer.appendChild(loadingContainer);
                    } else {
                        loadingContainer.style.display = 'block'
                    }

                    this.timeout = setTimeout(function () {
                        this.checkIsDeploying()
                    }.bind(this), 5000)

                } else {
                    let icon = iconContainer.querySelector('svg')
                    icon.style.display = 'block'

                    let loadingContainer = iconContainer.querySelector('div');
                    if (loadingContainer) {
                        loadingContainer.style.display = 'none'
                    }
                }
            },

        });

    Garnish.$doc.ready(function () {
        new Manager();
    });
})(jQuery);

