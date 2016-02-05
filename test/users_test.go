package codereview

import (
	"testing"
	"github.com/stretchr/testify/assert"
	_ "fmt"
	_ "reflect"
	"net/url"
	"time"
	"fmt"
)

func TestUser_RegisterAndLogin(t *testing.T) {
	cleanTables()

	c := NewClient()
	md5Str := md5password("123456")
	res := c.postData("users", url.Values{"mobilePhoneNumber": {"13261630925"},
		"username": {"lzwjavaTest"}, "smsCode": {"5555"}, "password":{md5Str}})
	assert.Equal(t, "lzwjavaTest", res["username"])
	assert.NotNil(t, res["userId"])
	assert.NotNil(t, res["created"])
	assert.NotNil(t, res["updated"]);

	res = c.postData("login", url.Values{"mobilePhoneNumber": {"13261630925"},
		"password": {md5password("123456")}});
	assert.Equal(t, "lzwjavaTest", res["username"])
	assert.Equal(t, "13261630925", res["mobilePhoneNumber"])
}

func TestUser_Update(t *testing.T) {
	c := NewClient()
	learner := registerUser(c)
	updated := learner["updated"].(string)
	avatarUrl := "http://7xotd0.com1.z0.glb.clouddn.com/header_logo.png"

	time.Sleep(time.Second)

	res := c.patchData("self", url.Values{"username": {"lzwjavaTest1"},
		"avatarUrl": {avatarUrl}})

	assert.Equal(t, "lzwjavaTest1", res["username"])
	assert.Equal(t, avatarUrl, res["avatarUrl"])
	assert.NotEqual(t, updated, res["updated"].(string))

	// Same username
	res = c.patchData("self", url.Values{"username": {"lzwjavaTest1"}});
	assert.Equal(t, "lzwjavaTest1", res["username"]);
}

func TestUser_Self(t *testing.T) {
	c := NewClient()
	user := registerUser(c)
	self := c.getData("self", url.Values{})
	assert.Equal(t, toInt(self["userId"]), toInt(user["userId"]))
	assert.Equal(t, self["username"].(string), user["username"].(string))
}

func TestUser_requestSmsCode(t *testing.T) {
	c := NewClient()
	res := c.post("requestSmsCode", url.Values{"mobilePhoneNumber": {"xx"}})
	fmt.Println(res)
}

