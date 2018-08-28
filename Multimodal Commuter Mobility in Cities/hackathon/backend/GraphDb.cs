using System;
using System.Collections.Generic;
using System.Configuration;
using System.Linq;
using System.Web;
using Neo4jClient;

namespace WebApi.Helpers
{
    public class GraphDb
    {
        private static GraphClient client = null;
        private static readonly object _syncLock = new object();
        public GraphClient GetClient()
        {
            var ip = ConfigurationManager.AppSettings["neo4jIp"];
            var port = ConfigurationManager.AppSettings["neo4jPort"];
            var username = ConfigurationManager.AppSettings["neo4jUserName"];
            var password = ConfigurationManager.AppSettings["neo4jPassword"];
            if (client == null)
            {
                lock (_syncLock)
                {
                    if (client == null)
                    {
                        client = new GraphClient(new Uri("http://" + ip + ":" + port + "/db/data"), username, password);
                        try
                        {
                            client.Connect();
                        }
                        catch (Exception e)
                        {
                            try
                            {
                                client.Connect();
                            }
                            catch (Exception ex)
                            {
                            }
                        }
                    }
                }
            }
            return client;
        }

     
    }
}