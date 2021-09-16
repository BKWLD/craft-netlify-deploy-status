/**
 * Craft Netlify Deploy Status plugin for Craft CMS
 *
 * Index Field JS
 *
 * @author    Isaaz Garcia
 * @copyright Copyright (c) 2021 Isaaz Garcia
 * @link      https://bukwild.com
 * @package   CraftNetlifyDeployStatus
 * @since     1.0.0
 */

(function($) {
    /** global: Craft */
    /** global: Garnish */
    var Manager = Garnish.Base.extend(
        {
            init: function() {
                this.addListener($('#new-webhook-btn'), 'activate', 'addNewWebhook');
                this.addListener($('#copy-url-btn'), 'activate', 'copyUrl');
                this.addListener($('#delete-webhook-btn'), 'activate', 'deleteWebhook');
            },

            copyUrl: function() {
                window.prompt("Copy to clipboard: Ctrl+C, Enter", $('#copy-url-btn').data( "url" ));
            },

            deleteWebhook: function() {
                var id = $('#delete-webhook-btn').data( "id" )
                var confirmMsg = $('#delete-webhook-btn').data( "confirm" )

                if (confirm(confirmMsg)){
                    Craft.postActionRequest('craft-netlify-deploy-status/webhook/delete', {id}, $.proxy(function(response, textStatus) {
                        if (textStatus === 'success') {
                            if (response.success) {
                                location.href = Craft.getUrl('craft-netlify-deploy-status/manage-webhooks');
                            }
                            else if (response.errors) {
                                var errors = this.flattenErrors(response.errors);
                                alert(Craft.t('craft-netlify-deploy-status', 'Could not delete the webhook:') + "\n\n" + errors.join("\n"));
                            }
                            else {
                                Craft.cp.displayError();
                            }
                        }
                    }, this));
                }
            },

            addNewWebhook: function() {
                var name = this.promptForWebhookName('');

                if (name) {
                    var data = {
                        name: name
                    };

                    Craft.postActionRequest('craft-netlify-deploy-status/webhook/save', data, $.proxy(function(response, textStatus) {
                        if (textStatus === 'success') {
                            if (response.success) {
                                location.href = Craft.getUrl('craft-netlify-deploy-status/manage-webhooks');
                            }
                            else if (response.errors) {
                                var errors = this.flattenErrors(response.errors);
                                alert(Craft.t('craft-netlify-deploy-status', 'Could not create the webhook:') + "\n\n" + errors.join("\n"));
                            }
                            else {
                                Craft.cp.displayError();
                            }
                        }

                    }, this));
                }
            },

            promptForWebhookName: function(oldName) {
                return prompt(Craft.t('craft-netlify-deploy-status', 'How do you want to name the webhook?'), oldName);
            },

            flattenErrors: function(responseErrors) {
                var errors = [];

                for (var attribute in responseErrors) {
                    if (!responseErrors.hasOwnProperty(attribute)) {
                        continue;
                    }

                    errors = errors.concat(responseErrors[attribute]);
                }

                return errors;
            }

        });


    Garnish.$doc.ready(function() {
        new Manager();
    });
})(jQuery);

