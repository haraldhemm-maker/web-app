/**
 * C O N F I G U R A T I O N  
 *  - Konfiguration.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class Configuration {
    static instance = null;

    // Private Konstruktor-Idee ueber statische Factory:
    static async getInstance() {
        if (Configuration.instance) {
            return Configuration.instance;
        }
        const config = new Configuration();
        const response = await config.getConfiguration();
        config.assignConfig(response);  // jetzt synchron, ohne await
        Configuration.instance = config;
        return config;
    }

    
    async getConfiguration() {
        const url = 'http://localhost/~harald/app/web-app/php/load_configuration.php';
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Konfiguration konnte nicht geladen werden: ${response.status}`);
        }
        return await response.json();
    }

    
    assignConfig(config) {
        for (const [key, value] of Object.entries(config)) {
            if (value !== null && typeof value === "object" && !Array.isArray(value)) {
                this[key] = this[key] ?? {};
                Object.assign(this[key], value);
            } 
            else {
                this[key] = value;
            }
        }
    }
} // Configuration
