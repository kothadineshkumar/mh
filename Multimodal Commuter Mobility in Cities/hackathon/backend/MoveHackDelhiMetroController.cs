using System;
using System.Collections.Generic;
using System.Device.Location;
using System.IO;
using System.Linq;
using System.Net;
using System.Web;
using System.Web.Http;
using System.Web.Mvc;
using AttributeRouting.Web.Http;
using ConfirmTkt.Models.Models.Alerts;
using ConfirmTkt.Models.Models.LocalTrain;
using ConfirmTkt.Models.Models.MoveHack;
using Neo4jClient;
using Neo4jClient.Cypher;
using Newtonsoft.Json.Linq;
using WebApi.Helpers;

namespace WebApi.Controllers
{
   
        [AttributeRouting.RoutePrefix("api/movehack/metro")]
        public class MoveHackDelhiMetroController : ApiController
        {
            public MoveHackDelhiMetroController()
            {
                
        }

            [System.Web.Http.HttpGet]
            [GET("getdistance")]
            public void UpdateDistance()
            {
                var lines = new List<string>();
                int count = 0;
                foreach (var line in File.ReadLines("C:\\Users\\Admin\\Desktop\\routes.csv"))
                {
                    try
                    {
                        var split = line.Split(',');
                        count++;
                        var so = int.Parse(split[0]);
                        var de = int.Parse(split[1]);
                        using (var ctx = new ConfirmTktEntities.ConfirmTktLogsEntities())
                        {
                            var row1 = ctx.MetroLatLongs.FirstOrDefault(x => x.Id == so);
                            var row2 = ctx.MetroLatLongs.FirstOrDefault(x => x.Id == de);
                            var sCoord = new GeoCoordinate((double)row1.Lat, (double)row1.Lng);
                            var eCoord = new GeoCoordinate((double)row2.Lat, (double)row2.Lat);

                            var dist = sCoord.GetDistanceTo(eCoord)/1000000;
                         
                            var li = so + "," + de + "," + dist + "," + "Metro" ;
                            lines.Add(li);
                        }
                    }
                    catch (Exception ex)
                    {

                    }
                }
                System.IO.File.WriteAllLines("C:\\Users\\Admin\\Desktop\\latlongnew.csv", lines);
        }

            [System.Web.Http.HttpGet]
            [GET("getlatlong")]
            public void GetLatLong()
            {
                var lines = new List<string>();
                foreach (var line in File.ReadLines("C:\\Users\\Admin\\Desktop\\file.csv"))
            {
                try
                {
                    var url = String.Format(
                        "https://maps.googleapis.com/maps/api/geocode/json?address={0}&key=AIzaSyBn5DGKA7Tcuu7KNBnoHWvvIGRkU6vNBOo",
                        line + " New Delhi");
                    WebClient webClient = new WebClient();
                    webClient.Encoding = System.Text.Encoding.UTF8;
                    string result = webClient.DownloadString(url);
                    var parsed = JObject.Parse(result);
                    var lat = parsed["results"][0]["geometry"]["location"]["lat"].ToString();
                    var lng = parsed["results"][0]["geometry"]["location"]["lng"].ToString();
                    var li = line.Replace(" Metro Station", "") + "," + lat + "," + lng;
                    lines.Add(li);
                }
                catch (Exception e)
                {
                    
                }
            }
                System.IO.File.WriteAllLines("C:\\Users\\Admin\\Desktop\\latlong.csv", lines);

        }

            [System.Web.Http.HttpGet]
        [GET("getroute")]
        public  RoutesMoveHackList GetRoutes(string source, string destination, string departure_time=null)
        {
            var routes = new  RoutesMoveHackList();
            source = source + " Delhi India";
            destination = destination + " Delhi India";
            if (departure_time == null)
                departure_time= ToEpoch(DateTime.UtcNow).ToString();
            routes.Routes = new List<RoutesMoveHack>();
           
            var bus = true;
            
            AddRoutes( routes);
            foreach (var ro in routes.Routes[0].Steps)
            {
                if (ro.Type == "METRO")
                    bus = false;
            }
            if (!bus)
            {
               
             //   AddRoutes(urlGet, routes);
            }

            return routes;

        }

            private static void AddRoutes( RoutesMoveHackList routes)
            {
            var path = new List<LocalTrainStation>();
                var path1 = new List<LocalTrainStation>();
                var source = routes.ToString();
                var destination = routes.ToString();
            //Connect to neo4j
            GraphClient graphClient = null;
                var time = "";
                if (string.IsNullOrEmpty(time))
                {
                    time = DateTime.Now.ToString("hh:mm tt");
                }
                var now = DateTime.Parse(time).TimeOfDay; ;
                try
                {
                    graphClient = new GraphDb().GetClient();
                    if (!graphClient.IsConnected)
                        graphClient.Connect();
                }
                catch (Exception e)
                {
                    graphClient = new GraphDb().GetClient();
                    graphClient.Connect();
                }
            //Neo4j Query
                var data =
                    "MATCH (start:Stations) , (end:Stations ) MATCH p = allShortestPaths((start) -[r:route *]->(end)) where start.Station =~ '(?i){0}' and end.Station =~ '(?i){1}' and  not start = end and  all(m2 in relationships(p) where  m2.time > 0) RETURN extract(n in nodes(p) | n.Station) as Names, reduce(sum = 0, nn IN relationships(p) | sum + nn.time) as sum order by sum limit 5";
                string query = String.Format(data,
                    source, destination);
                var result =
                    ((IRawGraphClient)graphClient).ExecuteGetCypherResults<PathReturn>(new CypherQuery(query, null,
                        Neo4jClient.Cypher.CypherResultMode.Projection));
                routes.Routes.AddRange((List<RoutesMoveHack>)result);



            }

            public static int ToEpoch(DateTime date)
            {
                if (date == null) return int.MinValue;
                DateTime epoch = new DateTime(1970, 1, 1);
                TimeSpan epochTimeSpan = date - epoch;
                return (int)epochTimeSpan.TotalSeconds;
            }
    }


    
}