import urllib2,urllib,os
import httplib

num = 144

while num < 1715:
    #http://www.christkirk.com/Sermons/mp3/1713.mp3
    base_url = "http://www.christkirk.com/Sermons/mp3/"
    file_name = str(num)+".mp3"
    url = base_url + file_name
    try:
        local_dir = os.getcwd() + '/media/douglas_wilson/' + file_name
    
        if not os.path.exists(local_dir):
            urllib.urlretrieve(url, local_dir)
            print str(num) + " downloaded."
        else:
            print str(num) + " skipped."
    except IOError:
        print str(num) + " failed."
    num = num + 1
