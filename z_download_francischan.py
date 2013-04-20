import urllib2, urllib, os
import httplib
import json


def extract_data(html):
    data = {'title':'', 'description':'', 'scripture':'', 'church':'', 'file_loc':'', 'file_loc_s3':''}
    if 'resourceLinks' in html:
        p = html.split('resourceLinks')[1][:220].replace('			<a href="/index.php?id=305&amp;file=', "").split("listenWindow")[0].replace('" class="', '')
        if 'fileadmin' in p:
            p = p[p.index('fileadmin'):]
            data['file_loc'] = "http://www.preachitteachit.org/"+p
            data['file_loc_s3'] = os.path.basename(data['file_loc'])
    
    if 'sermonTitle' in html:
        data['title'] = html.split('sermonTitle')[1].split("</h2>")[0].split('href')[1].split('</a>')[0].split('" >')[1]
        
    if 'sermonDescription' in html:
        data['description'] = html.split("sermonDescription")[1].split("</div>")[0].split("bodytext'>")[1].split("</p>")[0].replace("Used by permission.", "").replace("Preached at Cornerstone Church, Simi Valley, California.", "").replace("Preached at Cornerstone Church, Simi Valley California.", "").replace("From Cornerstone Church, Simi Valley, California.", "").replace("&#8221;", '"').replace("&#8220;", '"').replace("&nbsp;", " ").replace("&#8217;", "'").strip()
        
    if "Passages:" in html:
        p = html.split("Passages:")[1].split("</a></p>")[0]
        if 'target="_blank" >' in p:
            data['scripture'] = p.split('target="_blank" >')[1]
        
    data['church'] = "Cornerstone Church, Simi Valley, California"
        
    if data['file_loc'] == "":
        return -1
    data['download_me'] = 1
    data['author_name'] = "Francis Chan"
    data['church_website'] = "http://www.cornerstonesimi.com/"
    conn = urllib.urlopen(data['file_loc'])
    data['sermon_timestamp'] = conn.headers['last-modified']
    data['file_loc_s3'] =  "media/francis_chan/" + data['file_loc_s3']
    return data
            
    

#http://www.preachitteachit.org/fileadmin/Release_1/sermons/sermon_series/Frances_Chan/OHolyNightChan.mp3


urls = [
    "http://www.preachitteachit.org/about-us/the-team/francis-chan/sermons/",
    "http://www.preachitteachit.org/about-us/the-team/francis-chan/sermons/resource////1/",
    "http://www.preachitteachit.org/about-us/the-team/francis-chan/sermons/resource////2/",
    "http://www.preachitteachit.org/about-us/the-team/francis-chan/sermons/resource////3/",
    "http://www.preachitteachit.org/about-us/the-team/francis-chan/sermons/resource////4/"
]

for url in urls:
    html = urllib.urlopen(url).read()
    for p in html.split('sermonWrap'):
        x = extract_data(p)
        if x != -1:
        
            """
            local_dir = os.getcwd() + '/media/francis_chan/' + x['filename']
            if not os.path.exists(local_dir):
                urllib.urlretrieve(x['url'], local_dir)
                print x['filename'] + " downloaded."
            """
                    
                    
            url = 'http://localhost:8888/emethplayer/ajax.php?act=add_sermon'
            x['password'] = "royale"
            data = urllib.urlencode(x)
            req = urllib2.Request(url, data)
            response = urllib2.urlopen(req)
            print response.read()
